<?php

declare(strict_types=1);

use Akira\Setup\Actions\PublishConfigFilesAction;

it('publishes config files', function () {
    $action = new PublishConfigFilesAction();
    $result = $action->execute();
    expect($result)->toBeBool();
});
