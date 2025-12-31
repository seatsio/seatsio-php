<?php

namespace Seatsio\Events;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\GuzzleResponseDecoder;
use Seatsio\PageFetcher;
use Seatsio\Seasons\Season;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class Events
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Channels
     */
    public $channels;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->channels = new Channels($client);
    }

    public function create(string $chartKey, ?CreateEventParams $params = null): Event
    {
        $request = new stdClass();

        $request->chartKey = $chartKey;

        if ($params != null) {
            if ($params->eventKey !== null) {
                $request->eventKey = $params->eventKey;
            }

            if ($params->name !== null) {
                $request->name = $params->name;
            }

            if ($params->date !== null) {
                $request->date = $params->date->serialize();
            }

            if ($params->tableBookingConfig != null) {
                $request->tableBookingConfig = $this->serializeTableBookingConfig($params->tableBookingConfig);
            }

            if ($params->objectCategories !== null) {
                $request->objectCategories = $params->objectCategories;
            }

            if ($params->categories !== null) {
                $request->categories = $params->categories;
            }

            if ($params->channels !== null) {
                $request->channels = $params->channels;
            }

            if ($params->forSaleConfig !== null) {
                $request->forSaleConfig = $params->forSaleConfig;
            }
        }

        $res = $this->client->post('/events', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @return Event[]
     */
    public function createMultiple(string $chartKey, array $createEventParams): array
    {
        $request = new stdClass();
        $request->chartKey = $chartKey;
        $request->events = array();
        foreach ($createEventParams as $param) {
            $eventToCreate = new stdClass();

            if ($param->eventKey !== null) {
                $eventToCreate->eventKey = $param->eventKey;
            }

            if ($param->name !== null) {
                $eventToCreate->name = $param->name;
            }

            if ($param->date !== null) {
                $eventToCreate->date = $param->date->serialize();
            }

            if ($param->tableBookingConfig !== null) {
                $eventToCreate->tableBookingConfig = $param->tableBookingConfig;
            }

            if ($param->objectCategories !== null) {
                $eventToCreate->objectCategories = $param->objectCategories;
            }

            if ($param->categories !== null) {
                $eventToCreate->categories = $param->categories;
            }

            if ($param->channels !== null) {
                $eventToCreate->channels = $param->channels;
            }

            if ($param->forSaleConfig !== null) {
                $eventToCreate->forSaleConfig = $param->forSaleConfig;
            }

            $request->events[] = $eventToCreate;
        }

        $res = $this->client->post('/events/actions/create-multiple', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToObject($res);
        $mapper = SeatsioJsonMapper::create();

        return $mapper->mapArray($json->events, array(), 'Seatsio\Events\Event');
    }

    public function retrieve(string $eventKey): Event
    {
        $res = $this->client->get(UriTemplate::expand('/events/{key}', array("key" => $eventKey)));
        $json = GuzzleResponseDecoder::decodeToObject($res);
        $mapper = SeatsioJsonMapper::create();
        if ($json->isSeason) {
            return $mapper->map($json, new Season());
        }
        return $mapper->map($json, new Event());
    }

    public function update(string $eventKey, UpdateEventParams $params): void
    {
        $request = new stdClass();

        if ($params->eventKey !== null) {
            $request->eventKey = $params->eventKey;
        }

        if ($params->tableBookingConfig !== null) {
            $request->tableBookingConfig = $this->serializeTableBookingConfig($params->tableBookingConfig);
        }

        if ($params->objectCategories !== null) {
            $request->objectCategories = $params->objectCategories;
        }

        if ($params->categories !== null) {
            $request->categories = $params->categories;
        }

        if ($params->name !== null) {
            $request->name = $params->name;
        }

        if ($params->date !== null) {
            $request->date = $params->date->serialize();
        }

        if ($params->isInThePast !== null) {
            $request->isInThePast = $params->isInThePast;
        }

        $this->client->post(UriTemplate::expand('/events/{key}', array("key" => $eventKey)), ['json' => $request]);
    }

    public function delete(string $eventKey): void
    {
        $this->client->delete(UriTemplate::expand('/events/{key}', array("key" => $eventKey)));
    }

    public function listAll(): EventPagedIterator
    {
        return $this->iterator()->all();
    }

    public function listFirstPage(?int $pageSize = null): EventPage
    {
        return $this->iterator()->firstPage($pageSize);
    }

    public function listPageAfter(int $afterId, ?int $pageSize = null): EventPage
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    public function listPageBefore(int $beforeId, ?int $pageSize = null): EventPage
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    private function iterator(): EventLister
    {
        return new EventLister(new PageFetcher('/events', $this->client, function () {
            return new EventPage();
        }));
    }

    public function statusChanges(string $eventKey, ?string $filter = null, ?string $sortField = null, ?string $sortDirection = null): StatusChangeLister
    {
        return new StatusChangeLister(new PageFetcher(UriTemplate::expand('/events/{key}/status-changes', array("key" => $eventKey)), $this->client, function () {
            return new StatusChangePage();
        }, array("filter" => $filter, "sort" => self::toSort($sortField, $sortDirection))));
    }

    private static function toSort(?string $sortField, ?string $sortDirection): ?string
    {
        if (!$sortField) {
            return null;
        }
        if ($sortDirection) {
            return $sortField . ":" . $sortDirection;
        }
        return $sortField;
    }

    public function statusChangesForObject(string $eventKey, string $objectId): StatusChangeLister
    {
        return new StatusChangeLister(new PageFetcher(UriTemplate::expand('/events/{key}/objects/{objectId}/status-changes', array("key" => $eventKey, "objectId" => $objectId)), $this->client, function () {
            return new StatusChangePage();
        }));
    }

    /**
     * @param $forSale ObjectAndQuantity[]|null
     * @param $notForSale ObjectAndQuantity[]|null
     */
    public function editForSaleConfig(string $eventKey, ?array $forSale = null, ?array $notForSale = null): EditForSaleConfigResult
    {
        $request = new stdClass();
        if ($forSale !== null) {
            $request->forSale = array_map(function ($object) {
                return $object->toArray();
            }, $forSale);
        }
        if ($notForSale !== null) {
            $request->notForSale = array_map(function ($object) {
                return $object->toArray();
            }, $notForSale);
        }

        $res = $this->client->post(UriTemplate::expand('/events/{key}/actions/edit-for-sale-config', array("key" => $eventKey)), ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToObject($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new EditForSaleConfigResult());
    }

    /**
     * @return array<string, EditForSaleConfigResult>
     */
    public function editForSaleConfigForEvents(array $events): array
    {
        $request = new stdClass();
        $request->events = array_map(function ($params) {
            $paramsRequest = new stdClass();
            if ($params->forSale !== null) {
                $paramsRequest->forSale = array_map(function ($object) {
                    return $object->toArray();
                }, $params->forSale);
            }
            if ($params->notForSale !== null) {
                $paramsRequest->notForSale = array_map(function ($object) {
                    return $object->toArray();
                }, $params->notForSale);
            }
            return $paramsRequest;
        }, $events);

        $res = $this->client->post('/events/actions/edit-for-sale-config', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $event => $eventJson) {
            $result[$event] = $mapper->map($eventJson, new EditForSaleConfigResult());
        }
        return $result;
    }

    /**
     * @param $objects string[]|null
     * @param $categories string[]|null
     */
    public function replaceForSaleConfig(string $eventKey, bool $forSale, ?array $objects = null, ?array $areaPlaces = null, ?array $categories = null) {
        $action = $forSale ? 'mark-as-for-sale' : 'mark-as-not-for-sale';
        $request = new stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($areaPlaces !== null) {
            $request->areaPlaces = $areaPlaces;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(UriTemplate::expand('/events/{key}/actions/{action}', array("key" => $eventKey, "action" => $action)), ['json' => $request]);
    }

    /**
     * @param $objects string[]|null
     * @param $categories string[]|null
     * @deprecated
     */
    public function markAsForSale(string $eventKey, ?array $objects = null, ?array $areaPlaces = null, ?array $categories = null): void
    {
        self::replaceForSaleConfig($eventKey, true, $objects, $areaPlaces, $categories);
    }

    /**
     * @param $objects string[]|null
     * @param $categories string[]|null
     * @deprecated
     */
    public function markAsNotForSale(string $eventKey, ?array $objects = null, ?array $areaPlaces = null, ?array $categories = null): void
    {
        self::replaceForSaleConfig($eventKey, false, $objects, $areaPlaces, $categories);
    }

    public function markEverythingAsForSale(string $eventKey): void
    {
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-everything-as-for-sale', array("key" => $eventKey)));
    }

    public function overrideSeasonStatus(string $eventKey, array $objects)
    {
        $request = new stdClass();
        $request->objects = $objects;
        $this->client->post(UriTemplate::expand('/events/{key}/actions/override-season-status', array("key" => $eventKey)), ['json' => $request]);
    }

    public function useSeasonStatus(string $eventKey, array $objects)
    {
        $request = new stdClass();
        $request->objects = $objects;
        $this->client->post(UriTemplate::expand('/events/{key}/actions/use-season-status', array("key" => $eventKey)), ['json' => $request]);
    }

    public function updateExtraData(string $eventKey, string $object, array $extraData): void
    {
        $request = new stdClass();
        $request->extraData = $extraData;
        $this->client->post(
            UriTemplate::expand('/events/{key}/objects/{object}/actions/update-extra-data', ["key" => $eventKey, "object" => $object]),
            ['json' => $request]
        );
    }

    public function updateExtraDatas(string $eventKey, array $extraDatas): void
    {
        $request = new stdClass();
        $request->extraData = $extraDatas;
        $this->client->post(
            UriTemplate::expand('/events/{key}/actions/update-extra-data', ["key" => $eventKey]),
            ['json' => $request]
        );
    }

    public function retrieveObjectInfo(string $eventKey, string $objectLabel): EventObjectInfo
    {
        return $this->retrieveObjectInfos($eventKey, [$objectLabel])[$objectLabel];
    }

    /**
     * @param $objectLabels string[]
     * @return EventObjectInfo[]
     */
    public function retrieveObjectInfos(string $eventKey, array $objectLabels): array
    {
        $options = ['query' => Query::build(["label" => $objectLabels])];
        $res = $this->client->get(UriTemplate::expand('/events/{key}/objects', ["key" => $eventKey]), $options);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $objectLabel => $objectInfo) {
            $result[$objectLabel] = $mapper->map($objectInfo, new EventObjectInfo());
        }
        return $result;
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $channelKeys string[]|null
     * @param $allowedPreviousStatuses string[]|null
     * @param $rejectedPreviousStatuses string[]|null
     * @return ChangeObjectStatusResult
     */
    public function changeObjectStatus($eventKeyOrKeys, $objectOrObjects, string $status, ?string $holdToken = null, ?string $orderId = null,
                                       ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null,
                                       ?array $allowedPreviousStatuses = null, ?array $rejectedPreviousStatuses = null, $resaleListingId = null): ChangeObjectStatusResult
    {
        return $this->changeObjectStatusWithType($eventKeyOrKeys, $objectOrObjects, "CHANGE_STATUS_TO", $status, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys, $allowedPreviousStatuses, $rejectedPreviousStatuses, $resaleListingId);
    }

    private function changeObjectStatusWithType(
        $eventKeyOrKeys, $objectOrObjects, string $statusChangeCommandType, ?string $status = null, ?string $holdToken = null, ?string $orderId = null,
        ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null,
        ?array $allowedPreviousStatuses = null, ?array $rejectedPreviousStatuses = null, $resaleListingId = null): ChangeObjectStatusResult
    {
        $request = new stdClass();
        $request->objects = self::normalizeObjects($objectOrObjects);
        if ($statusChangeCommandType != null) {
            $request->type = $statusChangeCommandType;
        }
        if ($status != null) {
            $request->status = $status;
        }
        if ($holdToken !== null) {
            $request->holdToken = $holdToken;
        }
        if ($orderId !== null) {
            $request->orderId = $orderId;
        }
        if ($keepExtraData !== null) {
            $request->keepExtraData = $keepExtraData;
        }
        if ($ignoreChannels !== null) {
            $request->ignoreChannels = $ignoreChannels;
        }
        if ($channelKeys !== null) {
            $request->channelKeys = $channelKeys;
        }
        if ($allowedPreviousStatuses !== null) {
            $request->allowedPreviousStatuses = $allowedPreviousStatuses;
        }
        if ($rejectedPreviousStatuses !== null) {
            $request->rejectedPreviousStatuses = $rejectedPreviousStatuses;
        }
        if ($resaleListingId !== null) {
            $request->resaleListingId = $resaleListingId;
        }
        $request->events = is_array($eventKeyOrKeys) ? $eventKeyOrKeys : [$eventKeyOrKeys];
        $res = $this->client->post(
            '/events/groups/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'objects']]
        );
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new ChangeObjectStatusResult());
    }

    /**
     * @param $statusChangeRequests StatusChangeRequest[]
     * @return ChangeObjectStatusResult[]
     */
    public function changeObjectStatusInBatch(array $statusChangeRequests): array
    {
        $request = new stdClass();
        $request->statusChanges = array_map(function ($statusChangeRequest) {
            return $this->serializeStatusChangeRequest($statusChangeRequest);
        }, $statusChangeRequests);
        $res = $this->client->post(
            '/events/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'objects']]
        );
        $json = GuzzleResponseDecoder::decodeToObject($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json->results, array(), ChangeObjectStatusResult::class);
    }

    private function serializeStatusChangeRequest($statusChangeRequest)
    {
        $request = new stdClass();
        $request->type = $statusChangeRequest->type;
        $request->event = $statusChangeRequest->event;
        $request->objects = self::normalizeObjects($statusChangeRequest->objects);
        if ($statusChangeRequest->type == StatusChangeRequest::$TYPE_CHANGE_STATUS_TO) {
            $request->status = $statusChangeRequest->status;
        }
        if ($statusChangeRequest->holdToken !== null) {
            $request->holdToken = $statusChangeRequest->holdToken;
        }
        if ($statusChangeRequest->orderId !== null) {
            $request->orderId = $statusChangeRequest->orderId;
        }
        if ($statusChangeRequest->keepExtraData !== null) {
            $request->keepExtraData = $statusChangeRequest->keepExtraData;
        }
        if ($statusChangeRequest->ignoreChannels !== null) {
            $request->ignoreChannels = $statusChangeRequest->ignoreChannels;
        }
        if ($statusChangeRequest->channelKeys !== null) {
            $request->channelKeys = $statusChangeRequest->channelKeys;
        }
        if ($statusChangeRequest->allowedPreviousStatuses !== null) {
            $request->allowedPreviousStatuses = $statusChangeRequest->allowedPreviousStatuses;
        }
        if ($statusChangeRequest->rejectedPreviousStatuses !== null) {
            $request->rejectedPreviousStatuses = $statusChangeRequest->rejectedPreviousStatuses;
        }
        if ($statusChangeRequest->resaleListingId !== null) {
            $request->resaleListingId = $statusChangeRequest->resaleListingId;
        }
        return $request;
    }

    private function serializeTableBookingConfig($tableBookingConfig)
    {
        $request = new stdClass();
        $request->mode = $tableBookingConfig->mode;
        if ($tableBookingConfig->tables !== null) {
            $request->tables = $tableBookingConfig->tables;
        }
        return $request;
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $channelKeys string[]|null
     */
    public function book($eventKeyOrKeys, $objectOrObjects, ?string $holdToken = null, ?string $orderId = null, ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null): ChangeObjectStatusResult
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, EventObjectInfo::$BOOKED, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $channelKeys string[]|null
     */
    public function bookBestAvailable(string $eventKey, BestAvailableParams $bestAvailableParams, ?string $holdToken = null, ?string $orderId = null, ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null): BestAvailableObjects
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $bestAvailableParams, EventObjectInfo::$BOOKED, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @return ChangeObjectStatusResult
     */
    public function putUpForResale($eventKeyOrKeys, $objectOrObjects, $resaleListingId = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, EventObjectInfo::$RESALE, null, null, null, null, null, null, null, $resaleListingId);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $channelKeys string[]|null
     */
    public function release($eventKeyOrKeys, $objectOrObjects, ?string $holdToken = null, ?string $orderId = null, ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null): ChangeObjectStatusResult
    {
        return $this::changeObjectStatusWithType($eventKeyOrKeys, $objectOrObjects, "RELEASE", null, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $channelKeys string[]|null
     */
    public function hold($eventKeyOrKeys, $objectOrObjects, string $holdToken, ?string $orderId = null, ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null): ChangeObjectStatusResult
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, EventObjectInfo::$HELD, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $channelKeys string[]|null
     */
    public function holdBestAvailable(string $eventKey, BestAvailableParams $bestAvailableParams, ?string $holdToken, ?string $orderId = null, ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null): BestAvailableObjects
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $bestAvailableParams, EventObjectInfo::$HELD, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $channelKeys string[]|null
     */
    public function changeBestAvailableObjectStatus(string $eventKey, BestAvailableParams $bestAvailableParams, string $status, ?string $holdToken = null, ?string $orderId = null, ?bool $keepExtraData = null, ?bool $ignoreChannels = null, ?array $channelKeys = null): BestAvailableObjects
    {
        $request = new stdClass();
        $request->bestAvailable = new stdClass();
        $request->bestAvailable->number = $bestAvailableParams->number;
        if ($bestAvailableParams->categories !== null) {
            $request->bestAvailable->categories = $bestAvailableParams->categories;
        }
        if ($bestAvailableParams->extraData != null) {
            $request->bestAvailable->extraData = $bestAvailableParams->extraData;
        }
        if ($bestAvailableParams->ticketTypes !== null) {
            $request->bestAvailable->ticketTypes = $bestAvailableParams->ticketTypes;
        }
        if ($bestAvailableParams->tryToPreventOrphanSeats !== null) {
            $request->bestAvailable->tryToPreventOrphanSeats = $bestAvailableParams->tryToPreventOrphanSeats;
        }
        if ($bestAvailableParams->zone !== null) {
            $request->bestAvailable->zone = $bestAvailableParams->zone;
        }
        if ($bestAvailableParams->sections !== null) {
            $request->bestAvailable->sections = $bestAvailableParams->sections;
        }
        $request->status = $status;
        if ($holdToken !== null) {
            $request->holdToken = $holdToken;
        }
        if ($orderId !== null) {
            $request->orderId = $orderId;
        }
        if ($keepExtraData !== null) {
            $request->keepExtraData = $keepExtraData;
        }
        if ($ignoreChannels !== null) {
            $request->ignoreChannels = $ignoreChannels;
        }
        if ($channelKeys !== null) {
            $request->channelKeys = $channelKeys;
        }
        $res = $this->client->post(
            UriTemplate::expand('/events/{key}/actions/change-object-status', array("key" => $eventKey)),
            ['json' => $request]
        );
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new BestAvailableObjects());
    }

    public function moveEventToNewChartCopy(string $eventKey): Event
    {
        $res = $this->client->post(UriTemplate::expand('/events/{key}/actions/move-to-new-chart-copy', array("key" => $eventKey)));
        $json = GuzzleResponseDecoder::decodeToObject($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    private static function normalizeObjects($objectOrObjects): array
    {
        if (is_array($objectOrObjects)) {
            if (count($objectOrObjects) === 0) {
                return [];
            }
            return array_map(function ($object) {
                if ($object instanceof ObjectProperties) {
                    return $object->toArray();
                }
                if (is_string($object)) {
                    return ["objectId" => $object];
                }
                return $object;
            }, $objectOrObjects);
        }
        return self::normalizeObjects([$objectOrObjects]);
    }
}
