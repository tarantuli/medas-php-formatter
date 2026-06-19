<?php

namespace Medas\ServiceManager\BugTest;

use Medas\Core\Interfaces\{ErrorPolicy, ServiceConfigBuilder as ServiceConfigBuilderInterface};

/**
 * Bootstrap-time builder for ServiceConfig.
 * Holds MappingManager and PackageRegistry — the machinery needed to discover and register
 * packages. Once bootstrap is complete, call build() to get a plain, serializable ServiceConfig.
 */
class ServiceConfigBuilder implements ServiceConfigBuilderInterface
{
    private readonly ErrorPolicy $errorPolicy;

    public function __construct(
        ErrorPolicy|null $errorPolicy = null,
    )
    {
        $this->errorPolicy = $errorPolicy ?? new ErrorHandling\BasicErrorPolicy();
    }

    public function build(): ServiceConfig
    {
        return new ServiceConfig(
            errorPolicy: $this->errorPolicy::class,
        );
    }
}
