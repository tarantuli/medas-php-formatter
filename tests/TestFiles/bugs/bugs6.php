<?php

namespace Shared\Databases\MySql;

class StructureComparer
{
    /**
     * @param  array  $statements
     *
     * @return  array
     */
    public static function addInitializationAndFinalization(array $statements): array
    {
        if (!$statements) {
            return [];
        }

        $statements = array_merge(
            [
                'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0',
                'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0'
            ],
            $statements,
            ['SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS', 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS']
        );

        return $statements;
    }

    /**
     * @return  string[]
     */
    public function determineStatements(): array
    {
        $statements = array_merge(
            $this->determineStatementsForMissingTables(),
            $this->determineStatementsForModifiedTables(),
            $this->determineStatementsForObsoleteTables()
        );

        $statements = self::orderStatements($statements);
        $statements = StructureComparer::addInitializationAndFinalization($statements);

        return $statements;
    }

    /**
     * @return  string[]
     */
    private function determineStatementsForModifiedTables(): array
    {
        $sharedTables = array_intersect($this->sourceTables, $this->targetTables);
        $statements = [];

        foreach ($sharedTables as $sharedTable) {
            $targetTable = $this->targetDb->getTable($sharedTable);

            $builder = new AlterStatementBuilder(
                $targetTable->getStructure(),
                $this->sourceDb->getTable($sharedTable)->getStructure(),
                $targetTable->getConstraintsPerField()
            );

            $alterStatements = $builder->build();
            $statements = array_merge($statements, $alterStatements);
        }

        return $statements;
    }
}
