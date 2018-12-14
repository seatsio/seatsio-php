<?php

namespace Seatsio\Subaccounts;

class SubaccountListParams
{
    /**
     * @var string
     */
    public $filter;

    /**
     * @param $filter string
     */
    public function __construct($filter = null)
    {
        $this->filter = $filter;
    }

    /**
     * @param $filter string
     * @return $this
     */
    public function withFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function toArray()
    {
        $result = [];

        if ($this->filter !== null) {
            $result['filter'] = $this->filter;
        }

        return $result;
    }

}

