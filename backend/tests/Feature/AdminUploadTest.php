<?php

namespace Tests\Feature;

use App\Models\SongRequest;
use App\Services\Audio\AudioPreviewClipper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class AdminUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_audio_uploads_are_clipped_and_only_previews_are_persisted(): void
    {
        Storage::fake('public');

        $songRequest = SongRequest::create([
            'category' => 'geslaagd',
            'category_title' => 'Geslaagd',
            'email' => 'lara@example.com',
            'intake' => ['recipientName' => 'Lara'],
            'lyrics' => 'Definitieve lyrics',
            'final_lyrics' => 'Definitieve lyrics',
            'status' => 'music_prompt_ready',
            'price_cents' => 0,
            'paid_at' => now(),
        ]);

        $this->mock(AudioPreviewClipper::class, function (MockInterface $mock): void {
            $mock->shouldReceive('clipToTemporaryFile')
                ->times(4)
                ->andReturnUsing(function (): string {
                    $path = tempnam(sys_get_temp_dir(), 'clipped-preview-');
                    file_put_contents($path, 'clipped-00:30-00:45');

                    return $path;
                });
        });

        $this->get(route('admin.upload.show', ['token' => $songRequest->admin_upload_token]))
            ->assertOk()
            ->assertSee(
                'action="/admin/upload/'.$songRequest->admin_upload_token.'"',
                false,
            );

        $samples = [];
        foreach (range(1, 4) as $position) {
            $samples[] = [
                'title' => "Versie {$position}",
                'audio' => UploadedFile::fake()->create("full-song-{$position}.mp3", 100, 'audio/mpeg'),
                'cover' => UploadedFile::fake()->image("cover-{$position}.jpg"),
                'suno_url' => "https://suno.com/song/{$position}",
            ];
        }

        $this->post(route('admin.upload.store', ['token' => $songRequest->admin_upload_token]), [
            'samples' => $samples,
        ])->assertRedirect(route('admin.upload.show', ['token' => $songRequest->admin_upload_token]));

        $this->assertDatabaseCount('song_samples', 4);
        foreach ($songRequest->songSamples()->get() as $sample) {
            $this->assertStringEndsWith('.mp3', $sample->preview_path);
            Storage::disk('public')->assertExists($sample->preview_path);
            $this->assertSame('clipped-00:30-00:45', Storage::disk('public')->get($sample->preview_path));
        }

        $this->get(route('admin.upload.show', ['token' => $songRequest->admin_upload_token]))
            ->assertOk()
            ->assertSee('Mail naar klant versturen', false)
            ->assertSee(
                'action="/admin/upload/'.$songRequest->admin_upload_token.'/send"',
                false,
            );
    }
}
