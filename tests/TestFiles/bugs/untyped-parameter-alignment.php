<?php

class A
{
    public function __construct(
        private ImportsFinder $importsFinder,
                              $untyped,
        private NameFinder    $nameFinder,
    )
    {
    }
}
