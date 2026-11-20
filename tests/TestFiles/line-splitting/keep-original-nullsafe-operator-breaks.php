<?php

declare(strict_types=1);

// Keep enters before nullsafe object operators
$actionSet = $this->storageManager->controller($metaData->entity->storage)?->actionBuilders()
    ?->selectorAction()->build($selector, $arguments);
