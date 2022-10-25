<?php

namespace Seatsio\Events;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\UriTemplate\UriTemplate;
use GuzzleHttp\Utils;
use Seatsio\PageFetcher;
use Seatsio\Seasons\Season;
use Seatsio\SeatsioJsonMapper;
use stdClass;
use function Functional\map;

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

    public function create(string $chartKey, string $eventKey = null, TableBookingConfig $tableBookingConfig = null, string $socialDistancingRulesetKey = null, array $objectCategories = null): Event
    {
        $request = new stdClass();

        $request->chartKey = $chartKey;

        if ($eventKey !== null) {
            $request->eventKey = $eventKey;
        }

        if ($tableBookingConfig != null) {
            $request->tableBookingConfig = $this->serializeTableBookingConfig($tableBookingConfig);
        }

        if ($socialDistancingRulesetKey !== null) {
            $request->socialDistancingRulesetKey = $socialDistancingRulesetKey;
        }

        if ($objectCategories !== null) {
            $request->objectCategories = $objectCategories;
        }

        $res = $this->client->post('/events', ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @return Event[]
     */
    public function createMultiple(string $chartKey, array $eventCreationParams): array
    {
        $request = new stdClass();
        $request->chartKey = $chartKey;
        $request->events = array();
        foreach ($eventCreationParams as $param) {
            $eventToCreate = new stdClass();

            if ($param->eventKey !== null) {
                $eventToCreate->eventKey = $param->eventKey;
            }

            if ($param->tableBookingConfig !== null) {
                $eventToCreate->tableBookingConfig = $param->tableBookingConfig;
            }

            if ($param->socialDistancingRulesetKey !== null) {
                $eventToCreate->socialDistancingRulesetKey = $param->socialDistancingRulesetKey;
            }

            $request->events[] = $eventToCreate;
        }
        $res = $this->client->post('/events/actions/create-multiple', ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json->events, array(), 'Seatsio\Events\Event');
    }

    public function retrieve(string $eventKey): Event
    {
        $res = $this->client->get(UriTemplate::expand('/events/{key}', array("key" => $eventKey)));
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        if ($json->isSeason) {
            return $mapper->map($json, new Season());
        }
        return $mapper->map($json, new Event());
    }

    public function update(string $eventKey, string $chartKey = null, string $newEventKey = null, TableBookingConfig $tableBookingConfig = null, string $socialDistancingRulesetKey = null, $objectCategories = null): void
    {
        $request = new stdClass();

        if ($chartKey !== null) {
            $request->chartKey = $chartKey;
        }

        if ($newEventKey !== null) {
            $request->eventKey = $newEventKey;
        }

        if ($tableBookingConfig !== null) {
            $request->tableBookingConfig = $this->serializeTableBookingConfig($tableBookingConfig);
        }

        if ($socialDistancingRulesetKey !== null) {
            $request->socialDistancingRulesetKey = $socialDistancingRulesetKey;
        }

        if ($objectCategories !== null) {
            $request->objectCategories = $objectCategories;
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

    public function listFirstPage(int $pageSize = null): EventPage
    {
        return $this->iterator()->firstPage($pageSize);
    }

    public function listPageAfter(int $afterId, int $pageSize = null): EventPage
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    public function listPageBefore(int $beforeId, int $pageSize = null): EventPage
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    private function iterator(): EventLister
    {
        return new EventLister(new PageFetcher('/events', $this->client, function () {
            return new EventPage();
        }));
    }

    public function statusChanges(string $eventKey, string $filter = null, string $sortField = null, string $sortDirection = null): StatusChangeLister
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
     * @param $objects string[]|null
     * @param $categories string[]|null
     */
    public function markAsForSale(string $eventKey, array $objects = null, array $areaPlaces = null, array $categories = null): void
    {
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
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-as-for-sale', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $objects string[]|null
     * @param $categories string[]|null
     */
    public function markAsNotForSale(string $eventKey, array $objects = null, array $areaPlaces = null, array $categories = null): void
    {
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
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-as-not-for-sale', array("key" => $eventKey)), ['json' => $request]);
    }

    public function markEverythingAsForSale(string $eventKey): void
    {
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-everything-as-for-sale', array("key" => $eventKey)));
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
        $json = Utils::jsonDecode($res->getBody());
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
    public function changeObjectStatus($eventKeyOrKeys, $objectOrObjects, string $status, string $holdToken = null, string $orderId = null,
                                       bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null, bool $ignoreSocialDistancing = null,
                                       array $allowedPreviousStatuses = null, array $rejectedPreviousStatuses = null): ChangeObjectStatusResult
    {
        $request = new stdClass();
        $request->objects = self::normalizeObjects($objectOrObjects);
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
        if ($ignoreSocialDistancing !== null) {
            $request->ignoreSocialDistancing = $ignoreSocialDistancing;
        }
        if ($allowedPreviousStatuses !== null) {
            $request->allowedPreviousStatuses = $allowedPreviousStatuses;
        }
        if ($rejectedPreviousStatuses !== null) {
            $request->rejectedPreviousStatuses = $rejectedPreviousStatuses;
        }
        $request->events = is_array($eventKeyOrKeys) ? $eventKeyOrKeys : [$eventKeyOrKeys];
        $res = $this->client->post(
            '/events/groups/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'objects']]
        );
        $json = Utils::jsonDecode($res->getBody());
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
        $request->statusChanges = map($statusChangeRequests, function ($statusChangeRequest) {
            return $this->serializeStatusChangeRequest($statusChangeRequest);
        });
        $res = $this->client->post(
            '/events/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'objects']]
        );
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json->results, array(), ChangeObjectStatusResult::class);
    }

    private function serializeStatusChangeRequest($statusChangeRequest)
    {
        $request = new stdClass();
        $request->event = $statusChangeRequest->event;
        $request->objects = self::normalizeObjects($statusChangeRequest->objectOrObjects);
        $request->status = $statusChangeRequest->status;
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
    public function book($eventKeyOrKeys, $objectOrObjects, string $holdToken = null, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null, bool $ignoreSocialDistancing = null): ChangeObjectStatusResult
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, EventObjectInfo::$BOOKED, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys, $ignoreSocialDistancing);
    }

    /**
     * @param $categories string[]|null
     * @param $ticketTypes string[]|null
     * @param $channelKeys string[]|null
     */
    public function bookBestAvailable(string $eventKey, int $number, array $categories = null, string $holdToken = null, array $extraData = null, array $ticketTypes = null, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null): BestAvailableObjects
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $number, EventObjectInfo::$BOOKED, $categories, $holdToken, $extraData, $ticketTypes, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $channelKeys string[]|null
     */
    public function release($eventKeyOrKeys, $objectOrObjects, string $holdToken = null, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null): ChangeObjectStatusResult
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, EventObjectInfo::$FREE, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $channelKeys string[]|null
     */
    public function hold($eventKeyOrKeys, $objectOrObjects, string $holdToken, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null, bool $ignoreSocialDistancing = null): ChangeObjectStatusResult
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, EventObjectInfo::$HELD, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys, $ignoreSocialDistancing);
    }

    /**
     * @param $categories string[]|null
     * @param $ticketTypes string[]|null
     * @param $channelKeys string[]|null
     */
    public function holdBestAvailable(string $eventKey, int $number, string $holdToken, array $categories = null, array $extraData = null, array $ticketTypes = null, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null): BestAvailableObjects
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $number, EventObjectInfo::$HELD, $categories, $holdToken, $extraData, $ticketTypes, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $categories string[]|null
     * @param $ticketTypes string[]|null
     * @param $channelKeys string[]|null
     */
    public function changeBestAvailableObjectStatus(string $eventKey, int $number, string $status, array $categories = null, string $holdToken = null, array $extraData = null, array $ticketTypes = null, string $orderId = null, bool $keepExtraData = null, bool $ignoreChannels = null, array $channelKeys = null): BestAvailableObjects
    {
        $request = new stdClass();
        $bestAvailable = new stdClass();
        $bestAvailable->number = $number;
        if ($categories !== null) {
            $bestAvailable->categories = $categories;
        }
        if ($extraData != null) {
            $bestAvailable->extraData = $extraData;
        }
        if ($ticketTypes !== null) {
            $bestAvailable->ticketTypes = $ticketTypes;
        }
        $request->bestAvailable = $bestAvailable;
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
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new BestAvailableObjects());
    }

    private static function normalizeObjects($objectOrObjects): array
    {
        if (is_array($objectOrObjects)) {
            if (count($objectOrObjects) === 0) {
                return [];
            }
            return array_map(function ($object) {
                if ($object instanceof ObjectProperties) {
                    return $object;
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
