<?php

declare(strict_types=1);

use Akira\Setup\Actions\PublishWorkflowsAction;

it('publishes workflows', function () {
    $action = new PublishWorkflowsAction();
    $result = $action->execute();
    expect($result)->toBeBool();
});
