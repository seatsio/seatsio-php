<?php

namespace Seatsio\Events;

class ObjectAndQuantity
{
    public string $object;

    public ?int $quantity;

    public function __construct(string $object, ?int $quantity = null)
    {
        $this->object = $object;
        $this->quantity = $quantity;
    }

    public function toArray()
    {
        $result = ["object" => $this->object];
        if ($this->quantity !== null) {
            $result["quantity"] = $this->quantity;
        }
        return $result;
    }
}
