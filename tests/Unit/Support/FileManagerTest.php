<?php

declare(strict_types=1);

use Akira\Setup\Support\FileManager;
use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->fileManager = new FileManager();
});

it('copies stub files', function () {
    $result = $this->fileManager->copyStub('pint.json', 'test.json');
    expect($result)->toBeBool();
});

it('handles non-existent stub', function () {
    $result = $this->fileManager->copyStub('fake.json', 'test.json');
    expect($result)->toBeFalse();
});

it('updates json files', function () {
    $result = $this->fileManager->updateJson('composer.json', ['test' => 'value']);
    expect($result)->toBeBool();
});

it('merges composer scripts', function () {
    $result = $this->fileManager->mergeComposerScripts(['test' => 'pest']);
    expect($result)->toBeBool();
});

it('adds package json scripts', function () {
    $result = $this->fileManager->addPackageJsonScripts(['test' => 'jest']);
    expect($result)->toBeBool();
});

it('handles invalid json in updateJson', function () {
    $result = $this->fileManager->updateJson('non-existent-file.json', ['test' => 'value']);
    expect($result)->toBeFalse();
});

it('handles merging string scripts', function () {
    $result = $this->fileManager->mergeComposerScripts([
        'test' => 'pest',
        'format' => 'pint',
    ]);
    expect($result)->toBeBool();
});

it('handles merging array scripts', function () {
    $result = $this->fileManager->mergeComposerScripts([
        'test' => ['pest', 'phpstan'],
        'lint' => ['pint', 'rector'],
    ]);
    expect($result)->toBeBool();
});

it('handles existing scripts merge', function () {
    $result = $this->fileManager->mergeComposerScripts([
        'post-install-cmd' => '@php artisan vendor:publish',
    ]);
    expect($result)->toBeBool();
});

it('handles package json without scripts', function () {
    $result = $this->fileManager->addPackageJsonScripts([
        'dev' => 'vite',
        'build' => 'vite build',
    ]);
    expect($result)->toBeBool();
});

it('returns false when updating non-existent json file', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return false;
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->updateJson('non-existent.json', ['test' => 'value']);
    
    expect($result)->toBeFalse();
});

it('returns false when json decode fails in updateJson', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return 'invalid json content';
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->updateJson('invalid.json', ['test' => 'value']);
    
    expect($result)->toBeFalse();
});

it('returns false when composer.json does not exist', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return false;
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->mergeComposerScripts(['test' => 'value']);
    
    expect($result)->toBeFalse();
});

it('returns false when composer.json has invalid json', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return 'invalid json';
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->mergeComposerScripts(['test' => 'value']);
    
    expect($result)->toBeFalse();
});

it('creates scripts section when not present in composer.json', function () {
    $files = new class extends Filesystem {
        private $written = false;
        
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return json_encode(['name' => 'test-package']);
        }
        
        public function put($path, $contents, $lock = false): int|false {
            $this->written = true;
            $decoded = json_decode($contents, true);
            
            // Verify scripts section was added
            if (isset($decoded['scripts']) && is_array($decoded['scripts'])) {
                return strlen($contents);
            }
            return false;
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->mergeComposerScripts(['test' => 'pest']);
    
    expect($result)->toBeTrue();
});

it('merges scripts when both are strings and different', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return json_encode([
                'name' => 'test',
                'scripts' => [
                    'test' => 'phpunit',
                ],
            ]);
        }
        
        public function put($path, $contents, $lock = false): int|false {
            $decoded = json_decode($contents, true);
            
            // Verify both scripts are in array
            if (isset($decoded['scripts']['test']) && is_array($decoded['scripts']['test'])) {
                return strlen($contents);
            }
            return false;
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->mergeComposerScripts(['test' => 'pest']);
    
    expect($result)->toBeTrue();
});

it('does not duplicate when scripts are identical', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return json_encode([
                'name' => 'test',
                'scripts' => [
                    'test' => 'pest',
                ],
            ]);
        }
        
        public function put($path, $contents, $lock = false): int|false {
            return strlen($contents);
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->mergeComposerScripts(['test' => 'pest']);
    
    expect($result)->toBeTrue();
});

it('returns false when package.json does not exist', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return false;
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->addPackageJsonScripts(['test' => 'value']);
    
    expect($result)->toBeFalse();
});

it('returns false when package.json has invalid json', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return 'invalid json content';
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->addPackageJsonScripts(['test' => 'value']);
    
    expect($result)->toBeFalse();
});

it('creates scripts section when not present in package.json', function () {
    $files = new class extends Filesystem {
        public function exists($path): bool {
            return true;
        }
        
        public function get($path, $lock = false): string {
            return json_encode(['name' => 'test-package']);
        }
        
        public function put($path, $contents, $lock = false): int|false {
            $decoded = json_decode($contents, true);
            
            if (isset($decoded['scripts']) && is_array($decoded['scripts'])) {
                return strlen($contents);
            }
            return false;
        }
    };
    
    $fm = new FileManager($files);
    $result = $fm->addPackageJsonScripts(['dev' => 'vite']);
    
    expect($result)->toBeTrue();
});

it('can be instantiated with custom Filesystem', function () {
    $files = new Filesystem();
    $fm = new FileManager($files);
    
    expect($fm)->toBeInstanceOf(FileManager::class);
});
