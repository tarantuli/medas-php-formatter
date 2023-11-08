<?php

declare(strict_types=1);

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
