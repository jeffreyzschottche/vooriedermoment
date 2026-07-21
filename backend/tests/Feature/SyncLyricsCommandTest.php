<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class SyncLyricsCommandTest extends TestCase
{
    private string $temporaryRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->temporaryRoot = storage_path('framework/testing/lyrics-sync-'.bin2hex(random_bytes(5)));
        File::makeDirectory($this->temporaryRoot, 0755, true);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->temporaryRoot);

        parent::tearDown();
    }

    public function test_dry_run_reports_changes_without_writing_files(): void
    {
        $source = $this->temporaryRoot.'/source';
        $target = $this->temporaryRoot.'/target';
        $this->createCategory($source, 'verjaardag', 'nieuwe regel');
        $this->createCategory($target, 'verjaardag', 'oude regel');

        $this->artisan('lyrics:sync', [
            'source' => $source,
            '--target' => $target,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('4 geldig, 0 nieuw, 4 gewijzigd, 0 ongewijzigd.')
            ->expectsOutputToContain('Dry-run: er zijn geen bestanden aangepast.')
            ->assertSuccessful();

        $this->assertStringContainsString(
            'oude regel',
            File::get($target.'/verjaardag/verse1.json'),
        );
    }

    public function test_sync_adds_and_updates_valid_categories(): void
    {
        $source = $this->temporaryRoot.'/source';
        $target = $this->temporaryRoot.'/target';
        $this->createCategory($source, 'verjaardag', 'nieuwe regel');
        $this->createCategory($source, 'geslaagd', 'extra categorie');
        $this->createCategory($target, 'verjaardag', 'oude regel');

        $this->artisan('lyrics:sync', [
            'source' => $source,
            '--target' => $target,
            '--no-backup' => true,
        ])
            ->expectsOutputToContain('8 geldig, 4 nieuw, 4 gewijzigd, 0 ongewijzigd.')
            ->assertSuccessful();

        $this->assertStringContainsString(
            'nieuwe regel',
            File::get($target.'/verjaardag/verse1.json'),
        );
        $this->assertFileExists($target.'/geslaagd/chorus.json');
    }

    public function test_invalid_import_is_rejected_before_any_file_is_written(): void
    {
        $source = $this->temporaryRoot.'/source';
        $target = $this->temporaryRoot.'/target';
        $this->createCategory($source, 'verjaardag', 'nieuwe regel');
        $this->createCategory($target, 'verjaardag', 'oude regel');
        File::put($source.'/verjaardag/chorus.json', '{ongeldige json');

        $this->artisan('lyrics:sync', [
            'source' => $source,
            '--target' => $target,
        ])
            ->expectsOutputToContain('Ongeldige JSON in verjaardag/chorus.json')
            ->assertFailed();

        $this->assertStringContainsString(
            'oude regel',
            File::get($target.'/verjaardag/verse1.json'),
        );
    }

    public function test_sync_accepts_a_zip_with_a_lyrics_root_directory(): void
    {
        $source = $this->temporaryRoot.'/source';
        $target = $this->temporaryRoot.'/target';
        $zipPath = $this->temporaryRoot.'/lyrics.zip';
        $this->createCategory($source, 'verjaardag', 'regel uit zip');

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        foreach (File::allFiles($source) as $file) {
            $zip->addFile($file->getPathname(), 'lyrics/'.$file->getRelativePathname());
        }
        $zip->close();

        $this->artisan('lyrics:sync', [
            'source' => $zipPath,
            '--target' => $target,
        ])
            ->expectsOutputToContain('4 geldig, 4 nieuw, 0 gewijzigd, 0 ongewijzigd.')
            ->assertSuccessful();

        $this->assertStringContainsString(
            'regel uit zip',
            File::get($target.'/verjaardag/verse1.json'),
        );
    }

    private function createCategory(string $root, string $category, string $line): void
    {
        File::makeDirectory("{$root}/{$category}", 0755, true);

        foreach (['bridge', 'chorus', 'verse1', 'verse2'] as $section) {
            $lines = in_array($section, ['bridge', 'chorus'], true)
                ? [$line, 'tweede regel', 'derde regel', 'vierde regel']
                : [$line, 'tweede regel'];

            File::put(
                "{$root}/{$category}/{$section}.json",
                json_encode([
                    'section' => $section,
                    'category' => $category,
                    'description' => 'Testbestand',
                    'couplets' => [[
                        'id' => 1,
                        'lines' => $lines,
                        'rhyme_scheme' => count($lines) === 4 ? 'AABB' : 'AA',
                    ]],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            );
        }
    }
}
