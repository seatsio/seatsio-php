<?php

namespace Seatsio\Events;

use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;

class Events
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $chartKey string
     * @param $eventKey string
     * @param $bookWholeTables boolean
     * @return Event
     */
    public function create($chartKey, $eventKey = null, $bookWholeTables = null)
    {
        $request = new \stdClass();
        $request->chartKey = $chartKey;
        if ($eventKey !== null) {
            $request->eventKey = $eventKey;
        }
        if ($bookWholeTables !== null) {
            $request->bookWholeTables = $bookWholeTables;
        }
        $res = $this->client->post('/events', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @param $eventKey string
     * @return Event
     */
    public function retrieve($eventKey)
    {
        $res = $this->client->get(\GuzzleHttp\uri_template('/events/{key}', array("key" => $eventKey)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @param $eventKey string
     * @param $chartKey string
     * @param $newEventKey string
     * @param $bookWholeTables string
     * @return void
     */
    public function update($eventKey, $chartKey = null, $newEventKey = null, $bookWholeTables = null)
    {
        $request = new \stdClass();
        if ($chartKey !== null) {
            $request->chartKey = $chartKey;
        }
        if ($newEventKey !== null) {
            $request->eventKey = $newEventKey;
        }
        if ($bookWholeTables !== null) {
            $request->bookWholeTables = $bookWholeTables;
        }
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @return void
     */
    public function delete($eventKey)
    {
        $this->client->delete(\GuzzleHttp\uri_template('/events/{key}', array("key" => $eventKey)));
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
     * @param $objectId string
     * @return StatusChangeLister
     */
    public function statusChanges($eventKey, $objectId = null)
    {
        if ($objectId === null) {
            return new StatusChangeLister(new PageFetcher(\GuzzleHttp\uri_template('/events/{key}/status-changes', array("key" => $eventKey)), $this->client, function () {
                return new StatusChangePage();
            }));
        }
        return new StatusChangeLister(new PageFetcher(\GuzzleHttp\uri_template('/events/{key}/objects/{objectId}/status-changes', array("key" => $eventKey, "objectId" => $objectId)), $this->client, function () {
            return new StatusChangePage();
        }));
    }

    /**
     * @param $eventKey string
     * @param $objects string[]
     * @param $categories string[]
     * @return void
     */
    public function markAsForSale($eventKey, $objects = null, $categories = null)
    {
        $request = new \stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}/actions/mark-as-for-sale', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @param $objects string[]
     * @param $categories string[]
     * @return void
     */
    public function markAsNotForSale($eventKey, $objects = null, $categories = null)
    {
        $request = new \stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}/actions/mark-as-not-for-sale', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * @param $eventKey string
     * @return void
     */
    public function markEverythingAsForSale($eventKey)
    {
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}/actions/mark-everything-as-for-sale', array("key" => $eventKey)));
    }

    /**
     * @param $eventKey string
     * @param $object string
     * @param $extraData object|array
     * @return void
     */
    public function updateExtraData($eventKey, $object, $extraData)
    {
        $request = new \stdClass();
        $request->extraData = $extraData;
        $this->client->post(
            \GuzzleHttp\uri_template('/events/{key}/objects/{object}/actions/update-extra-data', ["key" => $eventKey, "object" => $object]),
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
        $request = new \stdClass();
        $request->extraData = $extraDatas;
        $this->client->post(
            \GuzzleHttp\uri_template('/events/{key}/actions/update-extra-data', ["key" => $eventKey]),
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
        $res = $this->client->get(\GuzzleHttp\uri_template('/events/{key}/objects/{object}', ["key" => $eventKey, "object" => $object]));
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
     * @return ChangeObjectStatusResult
     */
    public function changeObjectStatus($eventKeyOrKeys, $objectOrObjects, $status, $holdToken = null, $orderId = null)
    {
        $request = new \stdClass();
        $request->objects = self::normalizeObjects($objectOrObjects);
        $request->status = $status;
        if ($holdToken !== null) {
            $request->holdToken = $holdToken;
        }
        if ($orderId !== null) {
            $request->orderId = $orderId;
        }
        $request->events = is_array($eventKeyOrKeys) ? $eventKeyOrKeys : [$eventKeyOrKeys];
        $res = $this->client->post(
            '/seasons/actions/change-object-status',
            ['json' => $request, 'query' => ['expand' => 'labels']]
        );
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new ChangeObjectStatusResult());
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @return ChangeObjectStatusResult
     */
    public function book($eventKeyOrKeys, $objectOrObjects, $holdToken = null, $orderId = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, ObjectStatus::$BOOKED, $holdToken, $orderId);
    }

    /**
     * @param $eventKey string
     * @param $number int
     * @param $categories string[]
     * @param $holdToken string
     * @param $orderId string
     * @return BestAvailableObjects
     */
    public function bookBestAvailable($eventKey, $number, $categories = null, $holdToken = null, $orderId = null)
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $number, ObjectStatus::$BOOKED, $categories, $holdToken, $orderId);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @return ChangeObjectStatusResult
     */
    public function release($eventKeyOrKeys, $objectOrObjects, $holdToken = null, $orderId = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, ObjectStatus::$FREE, $holdToken, $orderId);
    }

    /**
     * @param $eventKeyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @return ChangeObjectStatusResult
     */
    public function hold($eventKeyOrKeys, $objectOrObjects, $holdToken, $orderId = null)
    {
        return $this::changeObjectStatus($eventKeyOrKeys, $objectOrObjects, ObjectStatus::$HELD, $holdToken, $orderId);
    }

    /**
     * @param $eventKey string
     * @param $number int
     * @param $categories string[]
     * @param $holdToken string
     * @param $orderId string
     * @return BestAvailableObjects
     */
    public function holdBestAvailable($eventKey, $number, $holdToken, $categories = null, $orderId = null)
    {
        return $this::changeBestAvailableObjectStatus($eventKey, $number, ObjectStatus::$HELD, $categories, $holdToken, $orderId);
    }

    /**
     * @param $eventKey string
     * @param $number int
     * @param $status string
     * @param $categories string[]
     * @param $holdToken string
     * @param $extraData array
     * @param $orderId string
     * @return BestAvailableObjects
     */
    public function changeBestAvailableObjectStatus($eventKey, $number, $status, $categories = null, $holdToken = null, $extraData = null, $orderId = null)
    {
        $request = new \stdClass();
        $bestAvailable = new \stdClass();
        $bestAvailable->number = $number;
        if ($categories !== null) {
            $bestAvailable->categories = $categories;
        }
        if ($extraData != null) {
            $bestAvailable->extraData = $extraData;
        }
        $request->bestAvailable = $bestAvailable;
        $request->status = $status;
        if ($holdToken !== null) {
            $request->holdToken = $holdToken;
        }
        if ($orderId !== null) {
            $request->orderId = $orderId;
        }
        $res = $this->client->post(
            \GuzzleHttp\uri_template('/events/{key}/actions/change-object-status', array("key" => $eventKey)),
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
            return array_map(function($object) {
                if($object instanceof ObjectProperties) {
                    return $object;
                }
                if(is_string($object)) {
                    return ["objectId" => $object];
                }
                return $object;
            }, $objectOrObjects);
        }
        return self::normalizeObjects([$objectOrObjects]);
    }

}