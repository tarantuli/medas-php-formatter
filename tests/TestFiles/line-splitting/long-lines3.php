<?php

declare(strict_types=1);

class AJDSJD
{
    public function adadad(): void
    {
        $actions = $this->storageController->actionBuilders()->get()->build(
            [$this->storageController->store($this->namingStrategy->determine($blueprint->storeRequestingOriginalClassStorage))],
            [$blueprint->idField()->name => $id]
        );
    }
}
