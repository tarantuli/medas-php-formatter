<?php

declare(strict_types=1);

namespace Medas\ServiceManager;

use Medas\ServiceManager\Attributes\Service;

#[Service(12)]
class ServiceInstantiator
{
    private MethodArgumentsValueResolver $methodArgumentsValueFinder;

    public function __construct(ServiceManager $manager)
    {
        $test = 'string';
        echo "This is a test with $test in a string";
        $this->methodArgumentsValueFinder = new MethodArgumentsValueResolver($manager);
        $manager->bindService($this, ServiceInstantiator::class);
    }

    public function instantiate(string $className): object
    {
        $class = new \ReflectionClass($className);

        $arguments = $this->getConstructorArgumentValues($class);

        return new $className(... $arguments);
    }

    private function getConstructorArgumentValues(\ReflectionClass $class): array
    {
        if (!$constructor = $class->getConstructor()) {
            return [];
        }

        return $this->methodArgumentsValueFinder->resolve($constructor);
    }

}
