<?php

namespace Seatsio\Events;

class StatusChangeRequest
{
    /**
     * @var string
     */
    public $event;

    /**
     * @var mixed
     */
    public $objectOrObjects;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $holdToken;

    /**
     * @var string
     */
    public $orderId;

    /**
     * @var boolean
     */
    public $keepExtraData;

    /**
     * @var boolean
     */
    public $ignoreChannels;

    /**
     * @var string[]
     */
    public $channelKeys;

    public function __construct($event, $objectOrObjects, $status, $holdToken = null, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null)
    {
        $this->event = $event;
        $this->objectOrObjects = $objectOrObjects;
        $this->status = $status;
        $this->holdToken = $holdToken;
        $this->orderId = $orderId;
        $this->keepExtraData = $keepExtraData;
        $this->ignoreChannels = $ignoreChannels;
        $this->channelKeys = $channelKeys;
    }
}
