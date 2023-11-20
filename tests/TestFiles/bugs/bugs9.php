<?php

declare(strict_types=1);

use Medas\ObjectInstantiator\Exceptions\MultipleImplementorsFoundForParameter;
use Medas\ServiceManager\Exceptions\MultipleImplementorsFound;

try {
    $result = $serviceManager->resolve($service);
}
catch (MultipleImplementorsFound $exception) {
    throw (new MultipleImplementorsFoundForParameter(
        $exception->type,
        $parameter->name,
        $parameter->getDeclaringClass()->name,
        $parameter->getDeclaringFunction()->name,
        $exception->implementors
    ))->setPrevious($exception);
}
