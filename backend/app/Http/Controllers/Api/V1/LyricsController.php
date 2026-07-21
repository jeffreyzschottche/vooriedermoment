<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Lyrics\LyricsGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LyricsController extends Controller
{
    protected LyricsGenerator $generator;

    public function __construct(LyricsGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Get available categories
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'categories' => $this->generator->getCategories(),
        ]);
    }

    /**
     * Get songform structure
     */
    public function songform(): JsonResponse
    {
        return response()->json($this->generator->getSongform());
    }

    /**
     * Preview available lyrics for a category
     */
    public function preview(string $category): JsonResponse
    {
        $categories = $this->generator->getCategories();

        if (!in_array($category, $categories)) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'category' => $category,
            'sections' => $this->generator->previewCategory($category),
        ]);
    }

    /**
     * Generate lyrics for a category with given context
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $category = (string) $request->input('category');
        $categories = $this->generator->getCategories();

        if (!in_array($category, $categories)) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }

        // Volledige (ruwe) intake doorgeven: de generator mapt zelf naar
        // placeholders en voedt de AI-slots met o.a. anecdotes/mustMention/tone.
        $intake = collect($request->except('category'))
            ->filter(fn ($value) => is_scalar($value))
            ->map(fn ($value) => (string) $value)
            ->all();

        $lyrics = $this->generator->generate($category, $intake);

        return response()->json($lyrics);
    }

    /**
     * Generate a complete song for an occasion without a fixed category.
     */
    public function generateGeneral(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'intake' => ['required', 'array'],
            'intake.occasion' => ['required', 'string', 'max:255'],
            'intake.recipientName' => ['required', 'string', 'max:255'],
            'intake.fromName' => ['nullable', 'string', 'max:255'],
            'intake.tone' => ['nullable', 'string', 'max:255'],
            'intake.vocals' => ['nullable', 'string', 'max:255'],
            'intake.musicStyle' => ['nullable', 'string', 'max:255'],
            'intake.tempo' => ['nullable', 'string', 'max:255'],
            'intake.anecdotes' => ['required', 'string', 'max:5000'],
            'intake.mustMention' => ['nullable', 'string', 'max:3000'],
            'intake.avoid' => ['nullable', 'string', 'max:1000'],
            'intake.additionalRecipientNames' => ['nullable', 'string', 'max:2000'],
            'intake.additionalSenderNames' => ['nullable', 'string', 'max:2000'],
        ]);

        return response()->json($this->generator->generateGeneral($validated['intake']));
    }
}
