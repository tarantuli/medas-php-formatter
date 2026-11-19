<?php

declare(strict_types=1);

use CloudInventor\Overs8\Entities\Products\ProductCostPeriod;

class StaticReturnType
{
    public function ofCostPeriod(ProductCostPeriod $costPeriod): static
    {
        $this->costPeriod = $costPeriod;
        $this->product = $costPeriod->product;

        return $this;
    }
}
