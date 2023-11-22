<?php

namespace Shared\Entities\Model\ClassBuilders;

use Shared\DataControl\Str;
use Shared\Entities\AbstractEntityProvider;

class AutoProviderClass extends AbstractClassBuilder
{
    /**
     * @return  string
     */
    public function getContent(): string
    {
        $parentProvider = $this->getParentProvider();

        $content = Str::replaceVariables(
            '<?php namespace {{namespace}};

            			use Shared\Databases\Interfaces\TableInterface;
            			use Shared\Shared;

            			/**
            			* Base instance provider for {{name}}
            			*
            			* The content of this file is generated automatically and should not be modified;
            			* please customize {{provider class fqcn}} instead
            			*/
            			abstract class {{class name}} extends {{parent class}}
            			{
            				public function getSingularName(): string
            				{
            					return  \'{{name}}\';
            				}

            				public function getPluralName(): string
            				{
            					return \'{{plural name}}\';
            				}

            				public function getEntityClass(): string
            				{
            					return  \\{{custom class fqcn}}::class;
            				}

            				/**
            				 * Returns active instances of \\{{custom class fqcn}}
            				 *
            				 * @param  array  $filters
            				 *
            				 * @return  \\{{custom class fqcn}}[]
            				 */
            				public function getActiveInstances(array $filters = []): array
            				{
            					return parent::getActiveInstances($filters);
            				}

            				/**
            				 * Returns instances of \\{{custom class fqcn}} as given by their IDs
            				 *
            				 * @param  array  $ids
            				 *
            				 * @return  \\{{custom class fqcn}}[]
            				 */
            				public function fromIds(array $ids): array
            				{
            					return parent::fromIDs($ids);
            				}

            				/**
            				 * Returns an instance of \\{{custom class fqcn}} given by ID
            				 *
            				 * @param  int       $id
            				 * @param array|null $data
            				 *
            				 * @return  \\{{custom class fqcn}}
            				 */
            				public function getInstance(int $id, array $data = null): \\{{custom class fqcn}}
            				{
            					return parent::getInstance($id, $data);
            				}

            				/**
            				 * Returns instances of \\{{custom class fqcn}}
            				 *
            				 * @param  array  $filters
            				 *
            				 * @return  \\{{custom class fqcn}}[]
            				 */
            				public function getInstances(array $filters = []): array
            				{
            					return parent::getInstances($filters);
            				}

            				/**
            				 * Creates an instance of \\{{custom class fqcn}}
            				 *
            				 * @param  array  $data
            				 *
            				 * @return  \\{{custom class fqcn}}
            				 */
            				public function createInstance(array $data = []): \\{{custom class fqcn}}
            				{
            					return {{creating parent}}::createInstance($data);
            				}

            				public function storage(): TableInterface
            				{
            					return Shared::db()->getTable(\'{{table name}}\');
            				}

            				public function storages(): array
            				{
            					$storages = parent::storages();
            					$storages[] = self::storage();

            					return $storages;
            				}
            			',
            [
                'namespace' => $this->getNamespace(),
                'name' => $this->entity->getName(),
                'plural name' => $this->entity->getPluralName(),
                'class name' => $this->getName(),
                'provider class fqcn' => $this->entity->getProviderClassBuilder()->getFullyQualifiedClassName(),
                'custom class fqcn' => $this->entity->getCustomClassBuilder()->getFullyQualifiedClassName(),
                'table name' => $this->entity->getEntityTableName(),
                'parent class' => $parentProvider,
                'creating parent' => $this->entity->getExtends() ? "\\" . AbstractEntityProvider::class : 'parent'
            ]
        );
    }
}
