<?php

namespace Seatsio\Events;

class StatusChangeRequest
{

    public static $TYPE_CHANGE_STATUS_TO = "CHANGE_STATUS_TO";
    public static $TYPE_RELEASE = "RELEASE";
    public static $TYPE_OVERRIDE_SEASON_STATUS = "OVERRIDE_SEASON_STATUS";
    public static $TYPE_USE_SEASON_STATUS = "USE_SEASON_STATUS";

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
    public $objects;

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

    /**
     * @var string
     */
    public $resaleListingId;

    public function setType(string $type) {
        $this->type = $type;
        return $this;
    }

    public function setEvent(string $event) {
        $this->event = $event;
        return $this;
    }

    public function setObjects($objects) {
        $this->objects = $objects;
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

    public function setResaleListingId(string $resaleListingId) {
        $this->resaleListingId = $resaleListingId;
        return $this;
    }
}
