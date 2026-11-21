<?php

$this->executor->execute($directory, function (Migration $migration): void {
    $unregistered = [];
},
// An existing store
// execution was not registered
fn(Migration $migration): bool
    => $this->isStoreMigration($migration::class));

$this->executor->execute($directory, function (Migration $migration): void {
    $unregistered = [];
},
// An existing store
fn(Migration $migration): bool
    => $this->isStoreMigration($migration::class));
