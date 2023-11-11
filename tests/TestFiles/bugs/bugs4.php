<?php

declare(strict_types=1);

class TestAsdfjdklfj
{
    public function test(): int
    {
        switch ($this->iterationType) {
            case self::LINEAR_ITERATION:
                return $this->min + $this->ii * $this->interval;

            case self::DAILY_ITERATION:
                return mktime(
                    0,
                    0,
                    0,
                    date('m', $this->min),
                    date('d', $this->min) + $this->ii * $this->interval / self::ONE_DAY,
                    date('Y', $this->min)
                );

            default:
                return 0;
        }
    }
}
