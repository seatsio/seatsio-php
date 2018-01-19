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
     * @param $eventKey string
     * @param $status string
     * @return array
     */
    public function byStatus($eventKey, $status = null)
    {
        $res = $this->client->get(self::reportUrl('byStatus', $eventKey, $status));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $status);
    }

    /**
     * @param $eventKey string
     * @param $categoryLabel string
     * @return array
     */
    public function byCategoryLabel($eventKey, $categoryLabel = null)
    {
        $res = $this->client->get(self::reportUrl('byCategoryLabel', $eventKey, $categoryLabel));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $categoryLabel);
    }

    /**
     * @param $eventKey string
     * @param $categoryKey string
     * @return array
     */
    public function byCategoryKey($eventKey, $categoryKey = null)
    {
        $res = $this->client->get(self::reportUrl('byCategoryKey', $eventKey, $categoryKey));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $categoryKey);
    }

    /**
     * @param $eventKey string
     * @param $label string
     * @return array
     */
    public function byLabel($eventKey, $label = null)
    {
        $res = $this->client->get(self::reportUrl('byLabel', $eventKey, $label));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $label);
    }

    /**
     * @param $eventKey string
     * @param $orderId string
     * @return array
     */
    public function byOrderId($eventKey, $orderId = null)
    {
        $res = $this->client->get(self::reportUrl('byOrderId', $eventKey, $orderId));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $orderId);
    }

    /**
     * @param $eventKey string
     * @param $section string
     * @return array
     */
    public function bySection($eventKey, $section = null)
    {
        $res = $this->client->get(self::reportUrl('bySection', $eventKey, $section));
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