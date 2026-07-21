<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use JsonException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

class SyncLyrics extends Command
{
    private const SECTIONS = ['bridge', 'chorus', 'verse1', 'verse2'];

    protected $signature = 'lyrics:sync
        {source : Map of ZIP-bestand met lyrics}
        {--dry-run : Alleen valideren en wijzigingen tonen}
        {--target= : Alternatieve doelmap (standaard database/data/lyrics)}
        {--no-backup : Geen back-up maken van bestanden die worden overschreven}';

    protected $description = 'Valideer en synchroniseer lyric-JSON vanuit een map of ZIP-bestand';

    public function handle(): int
    {
        try {
            $source = $this->resolveSource((string) $this->argument('source'));
            $target = $this->resolveTarget();
            $files = $this->readSource($source);

            $this->validateFiles($files);
            [$added, $changed, $unchanged] = $this->compareWithTarget($files, $target);

            $this->components->info(sprintf(
                '%d geldig, %d nieuw, %d gewijzigd, %d ongewijzigd.',
                count($files),
                count($added),
                count($changed),
                $unchanged,
            ));

            foreach ($added as $relativePath) {
                $this->line("  <fg=green>+</> {$relativePath}");
            }

            foreach ($changed as $relativePath) {
                $this->line("  <fg=yellow>~</> {$relativePath}");
            }

            if ($added === [] && $changed === []) {
                $this->components->info('De lyrics zijn al volledig gesynchroniseerd.');

                return self::SUCCESS;
            }

            if ($this->option('dry-run')) {
                $this->components->warn('Dry-run: er zijn geen bestanden aangepast.');

                return self::SUCCESS;
            }

            $backupPath = null;
            if ($changed !== [] && ! $this->option('no-backup')) {
                $backupPath = storage_path('app/private/lyrics-backups/'.now()->format('Ymd-His').'-'.Str::lower(Str::random(6)));
                $this->backupFiles($changed, $target, $backupPath);
            }

            foreach (array_merge($added, $changed) as $relativePath) {
                $this->writeAtomically($target.'/'.$relativePath, $files[$relativePath]);
            }

            $this->components->info(sprintf(
                'Synchronisatie voltooid: %d bestand(en) bijgewerkt.',
                count($added) + count($changed),
            ));

            if ($backupPath !== null) {
                $this->line("Back-up: {$backupPath}");
            }

            return self::SUCCESS;
        } catch (RuntimeException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function resolveSource(string $source): string
    {
        $path = realpath($source);

        if ($path === false || (! is_dir($path) && ! is_file($path))) {
            throw new RuntimeException("Bron bestaat niet: {$source}");
        }

        if (is_file($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'zip') {
            throw new RuntimeException('De bron moet een map of ZIP-bestand zijn.');
        }

        return $path;
    }

    private function resolveTarget(): string
    {
        $target = (string) ($this->option('target') ?: database_path('data/lyrics'));

        return rtrim($target, DIRECTORY_SEPARATOR);
    }

    /** @return array<string, string> */
    private function readSource(string $source): array
    {
        return is_dir($source)
            ? $this->readDirectory($source)
            : $this->readZip($source);
    }

    /** @return array<string, string> */
    private function readDirectory(string $source): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'json') {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($source) + 1);
            $relativePath = $this->normalizeRelativePath($relativePath);
            $contents = file_get_contents($file->getPathname());

            if ($contents === false) {
                throw new RuntimeException("Kan bronbestand niet lezen: {$file->getPathname()}");
            }

            if (array_key_exists($relativePath, $files)) {
                throw new RuntimeException("Dubbel lyricbestand in bron: {$relativePath}");
            }

            $files[$relativePath] = $contents;
        }

        ksort($files);

        return $files;
    }

    /** @return array<string, string> */
    private function readZip(string $source): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('De PHP zip-extensie is nodig om een ZIP-bestand te importeren.');
        }

        $zip = new ZipArchive;
        if ($zip->open($source) !== true) {
            throw new RuntimeException("Kan ZIP-bestand niet openen: {$source}");
        }

        $files = [];

        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = (string) $zip->getNameIndex($index);

                if (str_contains($name, '../') || str_starts_with($name, '/')) {
                    throw new RuntimeException("Onveilig pad in ZIP-bestand: {$name}");
                }

                if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'json') {
                    continue;
                }

                $relativePath = $this->normalizeRelativePath($name);
                $contents = $zip->getFromIndex($index);

                if ($contents === false) {
                    throw new RuntimeException("Kan bestand uit ZIP niet lezen: {$name}");
                }

                if (array_key_exists($relativePath, $files)) {
                    throw new RuntimeException("Dubbel lyricbestand in ZIP: {$relativePath}");
                }

                $files[$relativePath] = $contents;
            }
        } finally {
            $zip->close();
        }

        ksort($files);

        return $files;
    }

    private function normalizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $parts = array_values(array_filter(explode('/', $path), fn (string $part) => $part !== ''));

        if (($parts[0] ?? null) === 'lyrics') {
            array_shift($parts);
        }

        if (count($parts) !== 2 || in_array('..', $parts, true)) {
            throw new RuntimeException("Ongeldige lyriclocatie: {$path}; verwacht categorie/sectie.json.");
        }

        return implode('/', $parts);
    }

    /** @param array<string, string> $files */
    private function validateFiles(array $files): void
    {
        if ($files === []) {
            throw new RuntimeException('De bron bevat geen lyric-JSON-bestanden.');
        }

        $sectionsByCategory = [];

        foreach ($files as $relativePath => $contents) {
            [$category, $filename] = explode('/', $relativePath);
            $section = pathinfo($filename, PATHINFO_FILENAME);

            if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $category)) {
                throw new RuntimeException("Ongeldige categorieslug in {$relativePath}.");
            }

            if (! in_array($section, self::SECTIONS, true) || $filename !== "{$section}.json") {
                throw new RuntimeException("Onbekende sectie in {$relativePath}.");
            }

            try {
                $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new RuntimeException("Ongeldige JSON in {$relativePath}: {$exception->getMessage()}");
            }

            if (! is_array($data) || ($data['category'] ?? null) !== $category || ($data['section'] ?? null) !== $section) {
                throw new RuntimeException("Categorie of sectie in {$relativePath} komt niet overeen met het pad.");
            }

            if (! isset($data['couplets']) || ! is_array($data['couplets']) || $data['couplets'] === []) {
                throw new RuntimeException("{$relativePath} bevat geen coupletten.");
            }

            $ids = [];
            foreach ($data['couplets'] as $index => $couplet) {
                $location = $relativePath.' couplet '.($index + 1);
                $id = $couplet['id'] ?? null;
                $lines = $couplet['lines'] ?? null;
                $rhymeScheme = $couplet['rhyme_scheme'] ?? null;

                if (! is_int($id) || $id < 1 || in_array($id, $ids, true)) {
                    throw new RuntimeException("{$location} heeft een ongeldig of dubbel id.");
                }

                if (! is_array($lines) || $lines === [] || array_filter($lines, fn ($line) => ! is_string($line) || trim($line) === '')) {
                    throw new RuntimeException("{$location} heeft ongeldige regels.");
                }

                if (! is_string($rhymeScheme) || strlen($rhymeScheme) !== count($lines)) {
                    throw new RuntimeException("{$location} heeft een ongeldig rijmschema.");
                }

                $ids[] = $id;
            }

            $sectionsByCategory[$category][] = $section;
        }

        $expectedSections = self::SECTIONS;
        sort($expectedSections);

        foreach ($sectionsByCategory as $category => $sections) {
            sort($sections);
            if ($sections !== $expectedSections) {
                throw new RuntimeException("Categorie {$category} moet bridge, chorus, verse1 en verse2 bevatten.");
            }
        }
    }

    /**
     * @param  array<string, string>  $files
     * @return array{0: list<string>, 1: list<string>, 2: int}
     */
    private function compareWithTarget(array $files, string $target): array
    {
        $added = [];
        $changed = [];
        $unchanged = 0;

        foreach ($files as $relativePath => $contents) {
            $targetPath = $target.'/'.$relativePath;

            if (! is_file($targetPath)) {
                $added[] = $relativePath;
            } elseif (file_get_contents($targetPath) !== $contents) {
                $changed[] = $relativePath;
            } else {
                $unchanged++;
            }
        }

        return [$added, $changed, $unchanged];
    }

    /** @param list<string> $relativePaths */
    private function backupFiles(array $relativePaths, string $target, string $backupPath): void
    {
        foreach ($relativePaths as $relativePath) {
            $sourcePath = $target.'/'.$relativePath;
            $destinationPath = $backupPath.'/'.$relativePath;
            $this->ensureDirectory(dirname($destinationPath));

            if (! copy($sourcePath, $destinationPath)) {
                throw new RuntimeException("Back-up maken mislukt voor {$relativePath}.");
            }
        }
    }

    private function writeAtomically(string $path, string $contents): void
    {
        $this->ensureDirectory(dirname($path));
        $temporaryPath = tempnam(dirname($path), '.lyrics-');

        if ($temporaryPath === false) {
            throw new RuntimeException("Kan tijdelijk bestand niet maken voor {$path}.");
        }

        try {
            if (file_put_contents($temporaryPath, $contents, LOCK_EX) === false || ! rename($temporaryPath, $path)) {
                throw new RuntimeException("Kan lyricbestand niet schrijven: {$path}");
            }
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path) && ! mkdir($path, 0755, true) && ! is_dir($path)) {
            throw new RuntimeException("Kan map niet maken: {$path}");
        }
    }
}
