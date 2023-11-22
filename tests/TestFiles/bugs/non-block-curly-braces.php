<?php

declare(strict_types=1);

foreach ($propertyNames as $propertyName) {
    if (str_ends_with($propertyName, '()')) {
        $value = $entity?->{substr($propertyName, 0, -2)}();
    }
}
