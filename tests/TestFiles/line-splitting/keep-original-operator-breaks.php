<?php

declare(strict_types=1);

// Keep enters before object operators
$actionSet = $this->storageManager->controller($metaData->entity->storage)->actionBuilders()
    ->selectorAction()->build($selector, $arguments);
