<?php

declare(strict_types=1);

use Medas\Console\Text;
use Medas\Core\Collections\GenericCollection;
use Medas\StorageManager\{Interfaces\Record, Structure\Blueprint};
use My\ConfigValue;

/**
 * @extends GenericCollection<Record>
 */
class ClassNameNormalizer
{
    /** @var Blueprint\Field[] */
    public array $addFields = [];

    public function __construct(
        #[ConfigValue(GeneratorRootNamespace::class)]
        private string|null $rootNamespace,
    )
    {
        $metaData = $this->cacheManager->get()->get(
            [static::class, $className],
            function () use ($className) {
            return $this->compiler->compile($className);
        });

        $this->cachePurgeAmount = $purgeAmount ?: (int) floor($triggerSize / 4);

        $filePath
            ? $this->consolePrinter->print(new Text('created migration file '))
            : $this->consolePrinter->print(new Text('no need to create a migration file'));

        // Keep enters before object operators
        $actionSet = $this->storageManager->controller($metaData->entity->storage)->actionBuilders()
            ->selectorAction()->build($selector, $arguments);

        $actions = $this->storageController->actionBuilders()->get()->build(
            [$this->storageController->store($this->namingStrategy->determine($blueprint->storeRequestingOriginalClassStorage))],
            [$blueprint->idField()->name => $id]
        );

        if (isset($diff['minLength']) and $current->minLength <= $field->minLength) {
            unset($diff['minLength']);
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $useGuid ? self::PHP_GUID_TEMPLATE : self::PHP_INT_TEMPLATE
        );

        return $job->foundChanges ? $job->changes : null;
    }
}
