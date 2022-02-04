<?php

namespace Seatsio\Subaccounts;

class SubaccountListParams
{
    /**
     * @var string
     */
    public $filter;

    public function __construct(string $filter = null)
    {
        $this->filter = $filter;
    }

    public function withFilter(string $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->filter !== null) {
            $result['filter'] = $this->filter;
        }

        return $result;
    }

}

