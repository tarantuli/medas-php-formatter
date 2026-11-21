<?php

class A
{
    public function f(): void
    {
        if (!$docTag->isRepeatable() && isset($collected[$docTag->value])) {
            $this->warn(
                $job,
                "Docblock tag @{$tag->name} occurs more than once in some location {$location}"
            );
        }

        $response = $this->requestAs(
            $this->adminAccount(),
            Method::Get,
            "/products/{$product->id}/discount-analysis?period={$period}"
        );
    }
}
