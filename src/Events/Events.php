<?php

namespace Seatsio\Events;

use GuzzleHttp\Client;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;
use stdClass;
use function Functional\map;
use GuzzleHttp\UriTemplate\UriTemplate;

class Events
{

    /**
     * @var Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $chartKey string
     * @param $eventKey string
     * @param $tableBookingConfig TableBookingConfig
     * @param $socialDistancingRulesetKey string
     * @return Event
     */
    public function create($chartKey, $eventKey = null, $tableBookingConfig = null, $socialDistancingRulesetKey = null)
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

        $res = $this->client->post('/events', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @param $chartKey string
     * @param $eventCreationParams EventCreationParams[]
     * @return Event[]
     */
    public function createMultiple($chartKey, $eventCreationParams)
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
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json->events, array(), 'Seatsio\Events\Event');
    }

    /**
     * @param $eventKey string
     * @return Event
     */
    public function retrieve($eventKey)
    {
        $res = $this->client->get(UriTemplate::expand('/events/{key}', array("key" => $eventKey)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @param $eventKey string
     * @param $chartKey string
     * @param $newEventKey string
     * @param $tableBookingConfig TableBookingConfig
     * @param $socialDistancingRulesetKey string
     * @return void
     */
    public function update($eventKey, $chartKey = null, $newEventKey = null, $tableBookingConfig = null, $socialDistancingRulesetKey = null)
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

        $this->client->post(UriTemplate::expand('/events/{key}', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @return void
     */
    public function delete($eventKey)
    {
        $this->client->delete(UriTemplate::expand('/events/{key}', array("key" => $eventKey)));
    }

    /**
     * @return EventPagedIterator
     */
    public function listAll()
    {
        return $this->iterator()->all();
    }

    /**
     * @param $pageSize int
     * @return EventPage
     */
    public function listFirstPage($pageSize = null)
    {
        return $this->iterator()->firstPage($pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return EventPage
     */
    public function listPageAfter($afterId, $pageSize = null)
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return EventPage
     */
    public function listPageBefore($beforeId, $pageSize = null)
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    /**
     * @return EventLister
     */
    private function iterator()
    {
        return new EventLister(new PageFetcher('/events', $this->client, function () {
            return new EventPage();
        }));
    }

    /**
     * @param $eventKey string
     * @param $filter string
     * @param $sortField string
     * @param $sortDirection string
     * @return StatusChangeLister
     */
    public function statusChanges($eventKey, $filter = null, $sortField = null, $sortDirection = null)
    {
        return new StatusChangeLister(new PageFetcher(UriTemplate::expand('/events/{key}/status-changes', array("key" => $eventKey)), $this->client, function () {
            return new StatusChangePage();
        }, array("filter" => $filter, "sort" => self::toSort($sortField, $sortDirection))));
    }

    private static function toSort($sortField, $sortDirection)
    {
        if (!$sortField) {
            return null;
        }
        if ($sortDirection) {
            return $sortField . ":" . $sortDirection;
        }
        return $sortField;
    }

    /**
     * @param $eventKey string
     * @param $objectId string
     * @return StatusChangeLister
     */
    public function statusChangesForObject($eventKey, $objectId)
    {
        return new StatusChangeLister(new PageFetcher(UriTemplate::expand('/events/{key}/objects/{objectId}/status-changes', array("key" => $eventKey, "objectId" => $objectId)), $this->client, function () {
            return new StatusChangePage();
        }));
    }

    /**
     * @param $eventKey string
     * @param $channels object
     */
    public function updateChannels($eventKey, $channels)
    {
        $request = new stdClass();
        $request->channels = $channels;
        $this->client->post(UriTemplate::expand('/events/{key}/channels/update', array("key" => $eventKey)), ['json' => $request]);
    }

    public function assignObjectsToChannels($eventKey, $channelConfig)
    {
        $request = new stdClass();
        $request->channelConfig = $channelConfig;
        $this->client->post(UriTemplate::expand('/events/{key}/channels/assign-objects', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @param $objects string[]
     * @param $categories string[]
     * @return void
     */
    public function markAsForSale($eventKey, $objects = null, $categories = null)
    {
        $request = new stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-as-for-sale', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @param $objects string[]
     * @param $categories string[]
     * @return void
     */
    public function markAsNotForSale($eventKey, $objects = null, $categories = null)
    {
        $request = new stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-as-not-for-sale', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @return void
     */
    public function markEverythingAsForSale($eventKey)
    {
        $this->client->post(UriTemplate::expand('/events/{key}/actions/mark-everything-as-for-sale', array("key" => $eventKey)));
    }

    /**
     * @param $eventKey string
     * @param $object string
     * @param $extraData object|array
     * @return void
     */
    public function updateExtraData($eventKey, $object, $extraData)
    {
        $request = new stdClass();
        $request->extraData = $extraData;
        $this->client->post(
            UriTemplate::expand('/events/{key}/objects/{object}/actions/update-extra-data', ["key" => $eventKey, "object" => $object]),
            ['json' => $request]
        );
    }

    /**
     * @param $eventKey string
     * @param $extraDatas object|array
     * @return void
     */
    public function updateExtraDatas($eventKey, $extraDatas)
    {
        $request = new stdClass();
        $request->extraData = $extraDatas;
        $this->client->post(
            UriTemplate::expand('/events/{key}/actions/update-extra-data', ["key" => $eventKey]),
            ['json' => $request]
        );
    }

    /**
     * @param $eventKey string
     * @param $object string
     * @return ObjectStatus
     */
    public function retrieveObjectStatus($eventKey, $object)
    {
        $res = $this->client->get(UriTemplate::expand('/events/{key}/objects/{object}', ["key" => $eventKey, "object" => $object]));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new ObjectStatus());
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $status string
     * @param $holdToken string
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @param $ignoreSocialDistancing boolean
     * @return ChangeObjectStatusResult
     */
    public function changeObjectStatus($eventKeyOrKeys, $objectOrObjects, $status, $holdToken = null, $orderId = null,
                                       $keepExtraData = null, $ignoreChannels = null, $channelKeys = null, $ignoreSocialDistancing = null)
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
        $request->events = is_array($eventKeyOrKeys) ? $eventKeyOrKeys : [$eventKeyOrKeys];
        $res = $this->client->post(
            '/seasons/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'objects']]
        );
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new ChangeObjectStatusResult());
    }

    /**
     * @param $statusChangeRequests StatusChangeRequest[]
     * @return ChangeObjectStatusResult[]
     */
    public function changeObjectStatusInBatch($statusChangeRequests)
    {
        $request = new stdClass();
        $request->statusChanges = map($statusChangeRequests, function ($statusChangeRequest) {
            return $this->serializeStatusChangeRequest($statusChangeRequest);
        });
        $res = $this->client->post(
            '/events/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'objects']]
        );
        $json = \GuzzleHttp\json_decode($res->getBody());
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
     * @param $holdToken string
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @param $ignoreSocialDistancing boolean
     * @return ChangeObjectStatusResult
     */
    public function book($eventKeyOrKeys, $objectOrObjects, $holdToken = null, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null, $ignoreSocialDistancing = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, ObjectStatus::$BOOKED, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys, $ignoreSocialDistancing);
    }

    /**
     * @param $eventKey string
     * @param $number int
     * @param $categories string[]
     * @param $holdToken string
     * @param $extraData array
     * @param $ticketTypes string[]
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @return BestAvailableObjects
     */
    public function bookBestAvailable($eventKey, $number, $categories = null, $holdToken = null, $extraData = null, $ticketTypes = null, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null)
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $number, ObjectStatus::$BOOKED, $categories, $holdToken, $extraData, $ticketTypes, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @return ChangeObjectStatusResult
     */
    public function release($eventKeyOrKeys, $objectOrObjects, $holdToken = null, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, ObjectStatus::$FREE, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @param $ignoreSocialDistancing boolean
     * @return ChangeObjectStatusResult
     */
    public function hold($eventKeyOrKeys, $objectOrObjects, $holdToken, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null, $ignoreSocialDistancing = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, ObjectStatus::$HELD, $holdToken, $orderId, $keepExtraData, $ignoreChannels, $channelKeys, $ignoreSocialDistancing);
    }

    /**
     * @param $eventKey string
     * @param $number int
     * @param $categories string[]
     * @param $holdToken string
     * @param $extraData array
     * @param $ticketTypes string[]
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @return BestAvailableObjects
     */
    public function holdBestAvailable($eventKey, $number, $holdToken, $categories = null, $extraData = null, $ticketTypes = null, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null)
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $number, ObjectStatus::$HELD, $categories, $holdToken, $extraData, $ticketTypes, $orderId, $keepExtraData, $ignoreChannels, $channelKeys);
    }

    /**
     * @param $eventKey string
     * @param $number int
     * @param $status string
     * @param $categories string[]
     * @param $holdToken string
     * @param $extraData array
     * @param $ticketTypes string[]
     * @param $orderId string
     * @param $keepExtraData boolean
     * @param $ignoreChannels boolean
     * @param $channelKeys string[]
     * @return BestAvailableObjects
     */
    public function changeBestAvailableObjectStatus($eventKey, $number, $status, $categories = null, $holdToken = null, $extraData = null, $ticketTypes = null, $orderId = null, $keepExtraData = null, $ignoreChannels = null, $channelKeys = null)
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
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new BestAvailableObjects());
    }

    private static function normalizeObjects($objectOrObjects)
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
