<?php

namespace Shared\Entities\Model\ClassBuilders;

use Medas\Console\{Commands\ConsoleCommand, Formats\Color, Text};
use Medas\EntityManager\MetaData\Property;
use Shared\DataControl\Str;

class AutoProviderClassx extends AbstractClassBuilder
{
    /**
     * @return  string
     */
    public function getContent(): string
    {
        $filePath
            ? $this->consolePrinter->print(
                new Text('created migration file '),
                new Text($filePath, Color::LightYellow)
            )
            : $this->consolePrinter->print(new Text('no need to create a migration file', Color::LightGray));

        {
            usort(
                $this->processors,
                fn(ConsoleCommand $a, ConsoleCommand $b)
                    => strcasecmp($a->fullCommand(), $b->fullCommand()
                )
            );
        }

        $metaData->properties[] = new Property(
            name: $property->name,
            type: $type,
            hasDefault: $property->hasDefaultValue(),
            default: $property->hasDefaultValue() ? $property->getDefaultValue() : null,
            isId: !empty($property->getAttributes(Attributes\Id::class, \ReflectionAttribute::IS_INSTANCEOF)),
            isGeneratedValue: !empty($property->getAttributes(Attributes\IsGeneratedValue::class)),
            isCreationTimestamp: !empty($property->getAttributes(Attributes\IsCreationTimestamp::class)),
            isModificationTimestamp: !empty($property->getAttributes(Attributes\IsModificationTimestmap::class)),
            isNullable: $isNullable,
            isUnique: !empty($property->getAttributes(Attributes\IsUnique::class)),
            onDeleteCascade: !empty($property->getAttributes(Attributes\OnDeleteCascade::class)),
            phpTypes: $this->propertyTypeNormalizer->names($property),
            reflection: $property,
            handler: $handler ? $handler::class : null,
        );

        return Str::replaceVariables('<?php namespace {{namespace}};

            			/**
            			* {{description}}
            			*/
            			class {{name}} extends \{{auto class fqcn}}
            			{
            			}', [
            'description' => $this->entity->getDescription() ?: '(summary missing)',
            'namespace' => $this->getNamespace(),
            'name' => $this->getName(),
            'auto class fqcn' => $this->entity->getAutoClassBuilder()->getFullyQualifiedClassName(),
        ]);
    }
}
