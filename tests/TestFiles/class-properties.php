<?php

declare(strict_types=1);

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\{Attributes\Id, Attributes\IsGeneratedValue, Traits\Timestamps, Types\Guid};

class TimestampedPost implements HasId
{
    use Timestamps;

    #[Id, IsGeneratedValue]
    private int $id;

    public int $counter = 0;

    public function id(): int
    {
        return $this->id;
    }
}

class EntityWithDefaultValues
{
    #[Id, Guid]
    public string $id;

    private DateTime $dateTimeNotNullNoDefault;
    private DateTime|null $dateTimeNullNoDefault;
    private DateTime|null $dateTimeNullDefaultNull = null;
}
