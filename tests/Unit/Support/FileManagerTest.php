<?php

declare(strict_types=1);

use Akira\Setup\Support\FileManager;

it('copies stub files', function () {
    $fm = new FileManager();
    $result = $fm->copyStub('pint.json', 'test.json');
    expect($result)->toBeBool();
});

it('handles non-existent stub', function () {
    $fm = new FileManager();
    $result = $fm->copyStub('fake.json', 'test.json');
    expect($result)->toBeFalse();
});

it('updates json files', function () {
    $fm = new FileManager();
    $result = $fm->updateJson('composer.json', ['test' => 'value']);
    expect($result)->toBeBool();
});

it('merges composer scripts', function () {
    $fm = new FileManager();
    $result = $fm->mergeComposerScripts(['test' => 'pest']);
    expect($result)->toBeBool();
});

it('adds package json scripts', function () {
    $fm = new FileManager();
    $result = $fm->addPackageJsonScripts(['test' => 'jest']);
    expect($result)->toBeBool();
});

it('handles invalid json in updateJson', function () {
    $fm = new FileManager();
    $result = $fm->updateJson('non-existent-file.json', ['test' => 'value']);
    expect($result)->toBeFalse();
});

it('handles merging string scripts', function () {
    $fm = new FileManager();
    $result = $fm->mergeComposerScripts([
        'test' => 'pest',
        'format' => 'pint',
    ]);
    expect($result)->toBeBool();
});

it('handles merging array scripts', function () {
    $fm = new FileManager();
    $result = $fm->mergeComposerScripts([
        'test' => ['pest', 'phpstan'],
        'lint' => ['pint', 'rector'],
    ]);
    expect($result)->toBeBool();
});

it('handles existing scripts merge', function () {
    $fm = new FileManager();
    // This will merge with existing composer.json scripts
    $result = $fm->mergeComposerScripts([
        'post-install-cmd' => '@php artisan vendor:publish',
    ]);
    expect($result)->toBeBool();
});

it('handles package json without scripts', function () {
    $fm = new FileManager();
    $result = $fm->addPackageJsonScripts([
        'dev' => 'vite',
        'build' => 'vite build',
    ]);
    expect($result)->toBeBool();
});
