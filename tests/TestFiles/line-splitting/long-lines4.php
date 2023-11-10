<?php

declare(strict_types=1);

class Asdkfjsiofj
{
    private function processEntity(string $className): void
    {
        $expectedStructure = $this->entityStructureFinder->find($className);
        $needed = $this->storageManager->controller($entity->storage)->migrationBuilder()
            ->build(
                $this->storageManager->byName($entity->storage),
                $expectedStructure,
                $this->migrateMethod,
                $this->undoMethod
            );

        $this->migrationNeeded = $this->migrationNeeded || $needed;
    }
}
