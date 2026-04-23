<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

test('runtime code avoids raw database helper calls', function () {
    expect(findForbiddenPatternMatches(
        ['app', 'database', 'routes'],
        ['/\bDB::(?:select|statement|unprepared|raw)\s*\(/'],
    ))->toBeEmpty();
});

test('runtime code does not call env outside configuration files', function () {
    expect(findForbiddenPatternMatches(
        ['app', 'bootstrap', 'database', 'resources', 'routes'],
        ['/\benv\s*\(/'],
    ))->toBeEmpty();
});

test('runtime code avoids unbounded model all calls', function () {
    expect(findForbiddenPatternMatches(
        ['app', 'database', 'routes'],
        ['/::all\s*\(/'],
    ))->toBeEmpty();
});

test('blade templates do not execute database queries or aggregates', function () {
    expect(findForbiddenPatternMatches(
        ['resources/views'],
        [
            '/::(?:query|where|find|first|all|count|sum|avg|min|max)\s*\(/',
            '/\bDB::(?:select|statement|unprepared|raw)\s*\(/',
            '/->(?:count|sum|avg|min|max)\s*\(/',
        ],
    ))->toBeEmpty();
});

test('filament resources declare an optimized eloquent query override', function () {
    $resourceClasses = collect(File::allFiles(app_path('Filament/Resources')))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
        ->filter(fn (SplFileInfo $file): bool => Str::endsWith($file->getFilename(), 'Resource.php'))
        ->map(fn (SplFileInfo $file): string => resourceClassNameFromPath($file))
        ->values();

    expect($resourceClasses)->not->toBeEmpty();

    $resourceClasses->each(function (string $resourceClass): void {
        $reflectionMethod = new ReflectionMethod($resourceClass, 'getEloquentQuery');

        expect($reflectionMethod->getDeclaringClass()->getName())->toBe($resourceClass);
    });
});

/**
 * @param  list<string>  $directories
 * @param  list<string>  $patterns
 * @return list<string>
 */
function findForbiddenPatternMatches(array $directories, array $patterns): array
{
    $matches = collect($directories)
        ->flatMap(function (string $directory): array {
            $path = base_path($directory);

            if (! File::exists($path)) {
                return [];
            }

            return File::allFiles($path);
        })
        ->flatMap(function (SplFileInfo $file) use ($patterns): array {
            $contents = File::get($file->getPathname());
            $relativePath = Str::of($file->getPathname())
                ->after(base_path().DIRECTORY_SEPARATOR)
                ->toString();

            return collect($patterns)
                ->filter(fn (string $pattern): bool => preg_match($pattern, $contents) === 1)
                ->map(fn (string $pattern): string => "{$relativePath} :: {$pattern}")
                ->all();
        })
        ->values()
        ->all();

    sort($matches);

    return $matches;
}

function resourceClassNameFromPath(SplFileInfo $file): string
{
    $relativePath = Str::of($file->getPathname())
        ->after(app_path().DIRECTORY_SEPARATOR)
        ->replace(DIRECTORY_SEPARATOR, '\\')
        ->replace('.php', '')
        ->toString();

    return 'App\\'.$relativePath;
}
