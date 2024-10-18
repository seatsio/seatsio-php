<?php

namespace Seatsio\Events;

class StatusChangeRequest
{

    public static $TYPE_CHANGE_STATUS_TO = "CHANGE_STATUS_TO";
    public static $TYPE_RELEASE = "RELEASE";

    /**
     * @var string
     */
    public $type = "CHANGE_STATUS_TO";

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
     * @var bool
     */
    public $keepExtraData;

    /**
     * @var bool
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

    public function setType(string $type) {
        $this->type = $type;
        return $this;
    }

    public function setEvent(string $event) {
        $this->event = $event;
        return $this;
    }

    public function setObjectOrObjects($objectOrObjects) {
        $this->objectOrObjects = $objectOrObjects;
        return $this;
    }

    public function setStatus(string $status) {
        $this->status = $status;
        return $this;
    }

    public function setHoldToken(string $holdToken) {
        $this->holdToken = $holdToken;
        return $this;
    }

    public function setOrderId(string $orderId) {
        $this->orderId = $orderId;
        return $this;
    }

    public function setKeepExtraData(bool $keepExtraData) {
        $this->keepExtraData = $keepExtraData;
        return $this;
    }

    public function setIgnoreChannels(bool $ignoreChannels) {
        $this->ignoreChannels = $ignoreChannels;
        return $this;
    }

    public function setChannelKeys(array $channelKeys) {
        $this->channelKeys = $channelKeys;
        return $this;
    }

    public function setAllowedPreviousStatuses(array $allowedPreviousStatuses) {
        $this->allowedPreviousStatuses = $allowedPreviousStatuses;
        return $this;
    }

    public function setRejectedPreviousStatuses(array $rejectedPreviousStatuses) {
        $this->rejectedPreviousStatuses = $rejectedPreviousStatuses;
        return $this;
    }
}
