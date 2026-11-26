<?php

declare(strict_types=1);

class NoBlankLineInsideSplitArrayItem
{
    private function builtInFunctions(Job $job): array
    {
        return [
            'random' => fn(array $parameters) => $job->faker->randomElement($parameters[0]),
            'text' => fn() => '<p>'
                . implode('</p><p>', $job->faker->paragraphs($job->faker->numberBetween(1, 5)))
                . '</p>',

            'title' => fn() => rtrim($job->faker->sentence(), '. '),
        ];
    }
}
