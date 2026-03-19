#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

$svgDir = __DIR__.'/../resources/svg';
$outputDir = __DIR__.'/../src/Enums';
$stubFile = __DIR__.'/stubs/enum.stub';

if (! is_dir($svgDir)) {
    echo "SVG directory not found: {$svgDir}\n";
    exit(1);
}

$allIcons = collect(glob($svgDir.'/*.svg'))
    ->map(fn (string $file) => basename($file, '.svg'));

$default = $allIcons
    ->reject(fn (string $name) => Str::endsWith($name, ['-dark', '-light']))
    ->sort()
    ->values();

$dark = $allIcons
    ->filter(fn (string $name) => Str::endsWith($name, '-dark'))
    ->map(fn (string $name) => Str::beforeLast($name, '-dark'))
    ->sort()
    ->values();

$light = $allIcons
    ->filter(fn (string $name) => Str::endsWith($name, '-light'))
    ->map(fn (string $name) => Str::beforeLast($name, '-light'))
    ->sort()
    ->values();

generateEnum('Selfhst', $default, '', $stubFile, $outputDir);
generateEnum('SelfhstDark', $dark, '-dark', $stubFile, $outputDir);
generateEnum('SelfhstLight', $light, '-light', $stubFile, $outputDir);

function toCaseName(string $iconName): string
{
    $caseName = Str::studly($iconName);

    if (ctype_digit($caseName[0])) {
        return '_'.$caseName;
    }

    return $caseName;
}

function generateEnum(string $enumName, Collection $icons, string $valueSuffix, string $stubFile, string $outputDir): void
{
    $cases = $icons
        ->map(fn (string $baseName) => sprintf(
            '    case %s = \'%s\';',
            toCaseName($baseName),
            'selfhst-'.$baseName.$valueSuffix,
        ))
        ->implode("\n");

    $content = str_replace(
        ['{{ ENUM_NAME }}', '{{ CASES }}'],
        [$enumName, $cases],
        file_get_contents($stubFile),
    );

    if (! is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    file_put_contents($outputDir.'/'.$enumName.'.php', $content);

    echo "Generated {$enumName} with {$icons->count()} cases\n";
}
