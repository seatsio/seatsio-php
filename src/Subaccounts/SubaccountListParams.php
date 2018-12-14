<?php

namespace Seatsio\Subaccounts;

class SubaccountListParams
{
    /**
     * @var string
     */
    public $filter;
    /**
     * @var string
     */
    public $tag;
    /**
     * @var boolean
     */
    public $expandEvents;

    /**
     * @param $filter string
     * @param $tag string
     * @param $expandEvents boolean
     */
    public function __construct($filter = null, $tag = null, $expandEvents = null)
    {
        $this->filter = $filter;
        $this->tag = $tag;
        $this->expandEvents = $expandEvents;
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

    /**
     * @param $tag string
     * @return $this
     */
    public function withTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param $expandEvents boolean
     * @return $this
     */
    public function withExpandEvents($expandEvents)
    {
        $this->expandEvents = $expandEvents;
        return $this;
    }

    public function toArray()
    {
        $result = [];

        if ($this->filter !== null) {
            $result['filter'] = $this->filter;
        }
        if ($this->tag !== null) {
            $result['tag'] = $this->tag;
        }
        if ($this->expandEvents) {
            $result['expand'] = 'events';
        }

        return $result;
    }

}

