<?php

declare(strict_types=1);

use Medas\EntityManager\Types\Integer;
use Medas\PdoStorage\Exceptions\CantDetermineTypeFromDefinition;
use Medas\StorageManager\Structure\Blueprint\{Field, Type};

readonly class BinaryHandlerx
{
    public function handle(Field $field): string
    {
        $type = match (true) {
            $intMatch !== null => Type::Integer,

            $remainder->startsWith('varchar('), $remainder->startsWith('char('), $remainder->endsWith('text')
                => Type::Text,

            $remainder->startsWith('varbinary('), $remainder->startsWith('binary('), $remainder->endsWith('blob')
                => Type::Binary,

            $remainder->equals('datetime') => Type::DateTime,
            $remainder->equals('float') => Type::Float,
            default => throw new CantDetermineTypeFromDefinition((string) $remainder, $definition),
        };

        /** @noinspection PhpDuplicateMatchArmBodyInspection */
        return match (true) {
            $field->maxLength <= Integer::UNSIGNED_1_BYTE_MAX => $field->minLength === $field->maxLength
                ? sprintf('binary(%u)', $field->maxLength)
                : sprintf('varbinary(%u)', $field->maxLength),

            $field->maxLength <= Integer::UNSIGNED_2_BYTE_MAX => 'blob',
            $field->maxLength <= Integer::UNSIGNED_3_BYTE_MAX => 'mediumblob',
            $field->maxLength <= Integer::UNSIGNED_4_BYTE_MAX => 'longblob',
            default => 'blob'
        };
    }
}
