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

    /**
     * @var string[]
     */
    public $allowedPreviousStatuses;

    /**
     * @var string[]
     */
    public $rejectedPreviousStatuses;

    public function __construct(string $event, $objectOrObjects, string $status, string $holdToken = null, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null, array $allowedPreviousStatuses = null, array $rejectedPreviousStatuses = null)
    {
        $this->event = $event;
        $this->objectOrObjects = $objectOrObjects;
        $this->status = $status;
        $this->holdToken = $holdToken;
        $this->orderId = $orderId;
        $this->keepExtraData = $keepExtraData;
        $this->ignoreChannels = $ignoreChannels;
        $this->channelKeys = $channelKeys;
        $this->allowedPreviousStatuses = $allowedPreviousStatuses;
        $this->rejectedPreviousStatuses = $rejectedPreviousStatuses;
    }
}
