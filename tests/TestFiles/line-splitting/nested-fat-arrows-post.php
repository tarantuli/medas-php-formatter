<?php

declare(strict_types=1);

class NestedFatArrows
{
    private function builtInFunctions(Closure $evaluate): array
    {
        $branching = [
            'chance'
                => fn(array $parameters)
                    => $this->chanceProcessor->process($parameters, $evaluate),
            'if' => fn(array $parameters) => $this->ifProcessor->process($parameters, $evaluate),
            'switch'
                => fn(array $parameters)
                    => $this->switchProcessor->process($parameters, $evaluate),
        ];

        return $branching;
    }
}
