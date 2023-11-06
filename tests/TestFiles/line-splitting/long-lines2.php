<?php

declare(strict_types=1);

$this->settings->getXAxis()->autofitRange(
    $this->settings->getData()->getMinX()->getData()->getMinX(),
    $this->settings->getData()->getMaxX()->getData()->getMinX()
);
