<?php

namespace App\Http\Controllers;

use App\Mail\SampleChosenMail;
use App\Models\SongRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CustomerSampleController extends Controller
{
    public function showPassword(string $token)
    {
        $songRequest = SongRequest::where('selection_token', $token)->firstOrFail();

        // Check of samples bestaan
        if ($songRequest->songSamples->count() !== 4) {
            return view('customer.expired', ['message' => 'De samples zijn nog niet beschikbaar.']);
        }

        // Check of verlopen
        if ($songRequest->samplesExpired()) {
            return view('customer.expired', ['message' => 'Deze link is verlopen. Neem contact met ons op.']);
        }

        // Check of al gekozen
        if ($songRequest->chosen_sample_id) {
            return view('customer.already-chosen', ['songRequest' => $songRequest]);
        }

        return view('customer.password', [
            'songRequest' => $songRequest,
            'token' => $token,
        ]);
    }

    public function verifyPassword(Request $request, string $token)
    {
        $songRequest = SongRequest::where('selection_token', $token)->firstOrFail();

        $request->validate([
            'password' => 'required|string',
        ]);

        if ($request->password !== $songRequest->customer_password) {
            return back()->withErrors(['password' => 'Onjuist wachtwoord. Controleer je e-mail.']);
        }

        // Sla verificatie op in session
        session(['sample_verified_' . $token => true]);

        return redirect()->route('samples.listen', ['token' => $token]);
    }

    public function listen(string $token)
    {
        $songRequest = SongRequest::where('selection_token', $token)->firstOrFail();

        // Check session
        if (! session('sample_verified_' . $token)) {
            return redirect()->route('samples.password', ['token' => $token]);
        }

        // Check verlopen
        if ($songRequest->samplesExpired()) {
            return view('customer.expired', ['message' => 'Deze link is verlopen. Neem contact met ons op.']);
        }

        // Check al gekozen
        if ($songRequest->chosen_sample_id) {
            return view('customer.already-chosen', ['songRequest' => $songRequest]);
        }

        return view('customer.samples', [
            'songRequest' => $songRequest,
            'samples' => $songRequest->songSamples,
            'token' => $token,
        ]);
    }

    public function choose(Request $request, string $token)
    {
        $songRequest = SongRequest::where('selection_token', $token)->firstOrFail();

        // Check session
        if (! session('sample_verified_' . $token)) {
            return redirect()->route('samples.password', ['token' => $token]);
        }

        // Check verlopen
        if ($songRequest->samplesExpired()) {
            return view('customer.expired', ['message' => 'Deze link is verlopen.']);
        }

        // Check al gekozen
        if ($songRequest->chosen_sample_id) {
            return view('customer.already-chosen', ['songRequest' => $songRequest]);
        }

        $request->validate([
            'sample_id' => 'required|exists:song_samples,id',
        ]);

        $chosenSample = $songRequest->songSamples()->where('id', $request->sample_id)->firstOrFail();

        // Update song request
        $songRequest->update([
            'chosen_sample_id' => $chosenSample->id,
            'chosen_sample_position' => $chosenSample->position,
            'chosen_sample_title' => $chosenSample->title,
            'chosen_suno_source_url' => $chosenSample->suno_source_url,
            'status' => 'sample_chosen',
        ]);

        // Markeer sample als gekozen
        $chosenSample->update(['is_chosen' => true]);

        // Verwijder audio bestanden van ALLE samples (privacy)
        foreach ($songRequest->songSamples as $sample) {
            if ($sample->preview_path && Storage::disk($sample->storage_disk)->exists($sample->preview_path)) {
                Storage::disk($sample->storage_disk)->delete($sample->preview_path);
            }
            // Cover mag blijven voor admin referentie
        }

        $songRequest->update(['samples_deleted_at' => now()]);

        // Mail naar admin
        Mail::to(config('orders.notify_email'))->send(new SampleChosenMail($songRequest, $chosenSample));

        // Clear session
        session()->forget('sample_verified_' . $token);

        return view('customer.chosen', [
            'songRequest' => $songRequest,
            'chosenSample' => $chosenSample,
        ]);
    }
}
