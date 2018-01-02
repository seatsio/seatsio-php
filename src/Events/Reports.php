<?php

namespace Seatsio\Events;

use Seatsio\SeatsioJsonMapper;

class Reports
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
     * @param $key string
     * @param $status string
     * @return array
     */
    public function byStatus($key, $status = null)
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
    public function byCategoryLabel($key, $categoryLabel = null)
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
    public function byCategoryKey($key, $categoryKey = null)
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
    public function byLabel($key, $label = null)
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
    public function byUuid($key, $uuid = null)
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
    public function byOrderId($key, $orderId = null)
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
    public function bySection($key, $section = null)
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