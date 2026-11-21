<?php

$entity = $class->newLazyGhost(function (object $entity) use ($metaData, $identifier, $entityManager, $afterHydration): void {
    $entity->__construct();
});
