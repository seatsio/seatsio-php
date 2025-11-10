<?php

namespace Seatsio\Events;

class ForSaleConfigParams
{
    /**
     * @var ObjectAndQuantity[]|null
     */
    public ?array $forSale;

    /**
     * @var ObjectAndQuantity[]|null
     */
    public ?array $notForSale;

    /**
     * @param ObjectAndQuantity[]|null $forSale
     * @param ObjectAndQuantity[]|null $notForSale
     */
    public function __construct(?array $forSale = null, ?array $notForSale = null)
    {
        $this->forSale = $forSale;
        $this->notForSale = $notForSale;
    }
}
