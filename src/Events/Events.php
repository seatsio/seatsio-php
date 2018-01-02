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
     * @param $key string
     * @return Event
     */
    public function retrieve($key)
    {
        $res = $this->client->get(\GuzzleHttp\uri_template('/events/{key}', array("key" => $key)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Event());
    }

    /**
     * @param $key string
     * @param $chartKey string
     * @param $eventKey string
     * @param $bookWholeTables string
     * @return void
     */
    public function update($key, $chartKey = null, $eventKey = null, $bookWholeTables = null)
    {
        $request = new \stdClass();
        if ($chartKey !== null) {
            $request->chartKey = $chartKey;
        }
        if ($eventKey !== null) {
            $request->eventKey = $eventKey;
        }
        if ($bookWholeTables !== null) {
            $request->bookWholeTables = $bookWholeTables;
        }
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}', array("key" => $key)), ['json' => $request]);
    }

    /**
     * @return EventLister
     */
    public function iterator()
    {
        return new EventLister(new PageFetcher('/events', $this->client, function () {
            return new EventPage();
        }));
    }

    /**
     * @param $key string
     * @param $objectId string
     * @return StatusChangeLister
     */
    public function statusChanges($key, $objectId = null)
    {
        if ($objectId === null) {
            return new StatusChangeLister(new PageFetcher(\GuzzleHttp\uri_template('/events/{key}/status-changes', array("key" => $key)), $this->client, function () {
                return new StatusChangePage();
            }));
        }
        return new StatusChangeLister(new PageFetcher(\GuzzleHttp\uri_template('/events/{key}/objects/{objectId}/status-changes', array("key" => $key, "objectId" => $objectId)), $this->client, function () {
            return new StatusChangePage();
        }));
    }

    /**
     * @param $key string
     * @param $objects string[]
     * @param $categories string[]
     * @return void
     */
    public function markAsForSale($key, $objects = null, $categories = null)
    {
        $request = new \stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}/actions/mark-as-for-sale', array("key" => $key)), ['json' => $request]);
    }

    /**
     * @param $key string
     * @param $objects string[]
     * @param $categories string[]
     * @return void
     */
    public function markAsNotForSale($key, $objects = null, $categories = null)
    {
        $request = new \stdClass();
        if ($objects !== null) {
            $request->objects = $objects;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}/actions/mark-as-not-for-sale', array("key" => $key)), ['json' => $request]);
    }

    /**
     * @param $key string
     * @return void
     */
    public function markEverythingAsForSale($key)
    {
        $this->client->post(\GuzzleHttp\uri_template('/events/{key}/actions/mark-everything-as-for-sale', array("key" => $key)));
    }

    /**
     * @param $key string
     * @param $object string
     * @param $extraData object|array
     * @return void
     */
    public function updateExtraData($key, $object, $extraData)
    {
        $request = new \stdClass();
        $request->extraData = $extraData;
        $this->client->post(
            \GuzzleHttp\uri_template('/events/{key}/objects/{object}/actions/update-extra-data', ["key" => $key, "object" => $object]),
            ['json' => $request]
        );
    }

    /**
     * @param $key string
     * @param $object string
     * @return ObjectStatus
     */
    public function getObjectStatus($key, $object)
    {
        $res = $this->client->get(\GuzzleHttp\uri_template('/events/{key}/objects/{object}', ["key" => $key, "object" => $object]));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new ObjectStatus());
    }

    /**
     * @param $keyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $status string
     * @param $holdToken string
     * @param $orderId string
     * @return void
     */
    public function changeObjectStatus($keyOrKeys, $objectOrObjects, $status, $holdToken = null, $orderId = null)
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
        $request->events = is_array($keyOrKeys) ? $keyOrKeys : [$keyOrKeys];
        $this->client->post(
            '/seasons/actions/change-object-status',
            ['json' => $request]
        );
    }

    /**
     * @param $keyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @return void
     */
    public function book($keyOrKeys, $objectOrObjects, $holdToken = null, $orderId = null)
    {
        $this::changeObjectStatus($keyOrKeys, $objectOrObjects, "booked", $holdToken, $orderId);
    }

    /**
     * @param $keyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @return void
     */
    public function release($keyOrKeys, $objectOrObjects, $holdToken = null, $orderId = null)
    {
        $this::changeObjectStatus($keyOrKeys, $objectOrObjects, "free", $holdToken, $orderId);
    }

    /**
     * @param $keyOrKeys string|string[]
     * @param $objectOrObjects mixed
     * @param $holdToken string
     * @param $orderId string
     * @return void
     */
    public function hold($keyOrKeys, $objectOrObjects, $holdToken, $orderId = null)
    {
        $this::changeObjectStatus($keyOrKeys, $objectOrObjects, "reservedByToken", $holdToken, $orderId);
    }

    /**
     * @param $key string
     * @param $number int
     * @param $status string
     * @param $categories string[]
     * @param $useObjectUuidsInsteadOfLabels boolean
     * @param $holdToken string
     * @param $orderId string
     * @return BestAvailableObjects
     */
    public function changeBestAvailableObjectStatus($key, $number, $status, $categories = null, $useObjectUuidsInsteadOfLabels = null, $holdToken = null, $orderId = null)
    {
        $request = new \stdClass();
        $bestAvailable = new \stdClass();
        $bestAvailable->number = $number;
        if ($categories !== null) {
            $bestAvailable->categories = $categories;
        }
        if ($useObjectUuidsInsteadOfLabels !== null) {
            $bestAvailable->useObjectUuidsInsteadOfLabels = $useObjectUuidsInsteadOfLabels;
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
            \GuzzleHttp\uri_template('/events/{key}/actions/change-object-status', array("key" => $key)),
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
            if ($objectOrObjects[0] instanceof ObjectProperties) {
                return $objectOrObjects;
            }
            if (is_string($objectOrObjects[0])) {
                return array_map(function ($object) {
                    return ["objectId" => $object];
                }, $objectOrObjects);
            }
            return $objectOrObjects;
        }
        return self::normalizeObjects([$objectOrObjects]);
    }

    /**
     * @param $key string
     * @param $status string
     * @return array
     */
    public function reportByStatus($key, $status = null)
    {
        $res = $this->client->get(self::reportUrl('byStatus', $key, $status));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $status);
    }

    /**
     * @param $key string
     * @param $categoryLabel string
     * @return array
     */
    public function reportByCategoryLabel($key, $categoryLabel = null)
    {
        $res = $this->client->get(self::reportUrl('byCategoryLabel', $key, $categoryLabel));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $categoryLabel);
    }

    /**
     * @param $key string
     * @param $categoryKey string
     * @return array
     */
    public function reportByCategoryKey($key, $categoryKey = null)
    {
        $res = $this->client->get(self::reportUrl('byCategoryKey', $key, $categoryKey));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $categoryKey);
    }

    /**
     * @param $key string
     * @param $label string
     * @return array
     */
    public function reportByLabel($key, $label = null)
    {
        $res = $this->client->get(self::reportUrl('byLabel', $key, $label));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $label);
    }

    /**
     * @param $key string
     * @param $uuid string
     * @return array
     */
    public function reportByUuid($key, $uuid = null)
    {
        $res = $this->client->get(self::reportUrl('byUuid', $key, $uuid));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapSingleValuedReport($json, $uuid);
    }

    /**
     * @param $key string
     * @param $orderId string
     * @return array
     */
    public function reportByOrderId($key, $orderId = null)
    {
        $res = $this->client->get(self::reportUrl('byOrderId', $key, $orderId));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $orderId);
    }

    /**
     * @param $key string
     * @param $section string
     * @return array
     */
    public function reportBySection($key, $section = null)
    {
        $res = $this->client->get(self::reportUrl('bySection', $key, $section));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $section);
    }

    /**
     * @param $json mixed
     * @param $filter string
     * @return array
     */
    private static function mapMultiValuedReport($json, $filter)
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $status => $reportItems) {
            $result[$status] = $mapper->mapArray($reportItems, array(), EventReportItem::class);
        }
        if ($filter === null) {
            return $result;
        }
        return $result[$filter];
    }

    /**
     * @param $json mixed
     * @param $filter string
     * @return array
     */
    private static function mapSingleValuedReport($json, $filter)
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $status => $reportItem) {
            $result[$status] = $mapper->map($reportItem, new EventReportItem());
        }
        if ($filter === null) {
            return $result;
        }
        return $result[$filter];
    }

    private static function reportUrl($reportType, $eventKey, $filter)
    {
        if ($filter === null) {
            return \GuzzleHttp\uri_template('/reports/events/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
        }
        return \GuzzleHttp\uri_template('/reports/events/{key}/{reportType}/{filter}', array("key" => $eventKey, "reportType" => $reportType, "filter" => $filter));
    }

}