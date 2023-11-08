<?php

declare(strict_types=1);

use My\ClassName;

$function = function () use ($className) {
    return $this->compiler->compile($className);
};

while (true) {
    // A normal block
}

$metaData = $this->cacheManager->get()->get([static::class, $className], function () use ($className) {
    return $this->compiler->compile($className);
});

$metaData = $this->cacheManager->get()->get([static::class, $className], function () use ($className) {
    return $this->compiler->compile($className);
}, $b);

$method = function ($a, $b) use ($field): int|string|\ReflectionClass|ClassName {
    return strcmp($a->get($field), $b->get($field));
};
