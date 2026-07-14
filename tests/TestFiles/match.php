<?php

use Medas\EntityManager\Selector\{
    Calculations\Literal,
    Calculations\RowCount,
    Conditions\OrIs,
    Conditions\WhereContains,
    Conditions\WhereEndsWith,
    Conditions\WhereIn,
    Conditions\WhereIs,
    Conditions\WhereIsAtLeast,
    Conditions\WhereIsAtMost,
    Conditions\WhereIsLessThan,
    Conditions\WhereIsLessThanOrEqual,
    Conditions\WhereIsMoreThan,
    Conditions\WhereIsMoreThanOrEqual,
    Conditions\WhereIsNot,
    Conditions\WhereIsNotNull,
    Conditions\WhereIsNull,
    Conditions\WhereIsTruthy,
    Conditions\WhereNotIn,
    Conditions\WhereStartsWith,
    Exceptions\UnhandledCalculationType
};

$var = match (expression()) {
    true => $a,
    false => fn($a) => $a,
    fn($b) => null,
    ($i > 100) => 2,
    ($i < -100) => mb_strlen(2),
    $i >= 10 => self::create(),
    $i <= 10 => $this,
    $a, $b, $c => 'cheese',
    3 => 'three',
    a($a) || b($b) => 0,
    default => throw new Exception('oops'),
};

match ($calculation::class) {
    WhereIsTruthy::class => $this->processTruthy($job, $calculation),
    WhereIs::class => $this->processComparison($job, $calculation, '='),
    WhereIsNot::class => $this->processComparison($job, $calculation, '!='),
    WhereIn::class => $this->processComparison($job, $calculation, ' in '),
    WhereNotIn::class => $this->processComparison($job, $calculation, ' not in '),
    WhereIsMoreThan::class => $this->processComparison($job, $calculation, '>'),
    WhereIsLessThan::class => $this->processComparison($job, $calculation, '<'),

    WhereIsMoreThanOrEqual::class, WhereIsAtLeast::class
        => $this->processComparison($job, $calculation, '>='),

    WhereIsLessThanOrEqual::class, WhereIsAtMost::class
        => $this->processComparison($job, $calculation, '<='),

    WhereIsNull::class => $this->processNullComparison($job, $calculation, true),
    WhereIsNotNull::class => $this->processNullComparison($job, $calculation, false),
    WhereContains::class => $this->processLikeComparison($job, $calculation, '%', '%'),
    WhereStartsWith::class => $this->processLikeComparison($job, $calculation, '', '%'),
    WhereEndsWith::class => $this->processLikeComparison($job, $calculation, '%', ''),
    RowCount::class => $this->processRowCount($job),
    Literal::class => $this->processLiteral($job, $calculation),
    OrIs::class => $this->processOrIs($job, $calculation),
    default => throw new UnhandledCalculationType($calculation),
};
