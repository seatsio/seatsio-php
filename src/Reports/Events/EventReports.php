<?php

namespace Seatsio\Reports\Events;

use Seatsio\SeatsioJsonMapper;
use function GuzzleHttp\uri_template;

class EventReports
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
     * @return array
     */
    public function summaryByStatus($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byStatus', $eventKey));
        $json = \GuzzleHttp\json_decode($res->getBody(), true);
        return $json;
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryByStatus($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byStatus', $eventKey));
        $json = \GuzzleHttp\json_decode($res->getBody(), true);
        return $json;
    }

    /**
     * @param $eventKey string
     * @param $objectType string
     * @return array
     */
    public function byObjectType($eventKey, $objectType = null)
    {
        $res = $this->client->get(self::reportUrl('byObjectType', $eventKey, $objectType));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $objectType);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function summaryByObjectType($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byObjectType', $eventKey));
        $json = \GuzzleHttp\json_decode($res->getBody(), true);
        return $json;
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryByObjectType($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byObjectType', $eventKey));
        $json = \GuzzleHttp\json_decode($res->getBody(), true);
        return $json;
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
     * @return array
     */
    public function summaryByCategoryLabel($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byCategoryLabel', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryByCategoryLabel($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byCategoryLabel', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
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
     * @return array
     */
    public function summaryByCategoryKey($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byCategoryKey', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryByCategoryKey($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byCategoryKey', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
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
     * @param $eventKey string
     * @return array
     */
    public function summaryBySection($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('bySection', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryBySection($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('bySection', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @param $channel string
     * @return array
     */
    public function byChannel($eventKey, $channel = null)
    {
        $res = $this->client->get(self::reportUrl('byChannel', $eventKey, $channel));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $channel);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function summaryByChannel($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byChannel', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryByChannel($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byChannel', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @param $selectability string
     * @return array
     */
    public function bySelectability($eventKey, $selectability = null)
    {
        $res = $this->client->get(self::reportUrl('bySelectability', $eventKey, $selectability));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json, $selectability);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function summaryBySelectability($eventKey)
    {
        $res = $this->client->get(self::summaryReportUrl('bySelectability', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $eventKey string
     * @return array
     */
    public function deepSummaryBySelectability($eventKey)
    {
        $res = $this->client->get(self::deepSummaryReportUrl('bySelectability', $eventKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
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
        if (array_key_exists($filter, $result)) {
            return $result[$filter];
        }
        return [];
    }

    private static function reportUrl($reportType, $eventKey, $filter)
    {
        if ($filter === null) {
            return uri_template('/reports/events/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
        }
        return uri_template('/reports/events/{key}/{reportType}/{filter}', array("key" => $eventKey, "reportType" => $reportType, "filter" => $filter));
    }

    private static function summaryReportUrl($reportType, $eventKey)
    {
        return uri_template('/reports/events/{key}/{reportType}/summary', array("key" => $eventKey, "reportType" => $reportType));
    }

    private static function deepSummaryReportUrl($reportType, $eventKey)
    {
        return uri_template('/reports/events/{key}/{reportType}/summary/deep', array("key" => $eventKey, "reportType" => $reportType));
    }

}
