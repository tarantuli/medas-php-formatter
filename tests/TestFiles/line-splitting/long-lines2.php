<?php

declare(strict_types=1);

$this->settings->getXAxis()->autofitRange(
    $this->settings->getData()->getMinX()->getData()->getMinX(),
    $this->settings->getData()->getMaxX()->getData()->getMinX()
);

$a = 0x1000000 * $b->to255($this->getTransparency())
    + 0x10000 * $b->to255($this->getRed())
    + 0x100 * $b->to255($this->getGreen())
    + $b->to255($this->getBlue());

$b = 10;
