<?php

namespace Seatsio\Reports\Charts;

use GuzzleHttp\Client;
use Seatsio\SeatsioJsonMapper;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\Charts\ChartObjectInfo;

class ChartReports
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
     * @param $bookWholeTables string
     * @return array
     */
    public function byLabel($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $objectType string
     * @return array
     */
    public function byObjectType($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byObjectType', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @return array
     */
    public function summaryByObjectType($chartKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byObjectType', $chartKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function byCategoryKey($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byCategoryKey', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @return array
     */
    public function summaryByCategoryKey($chartKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byCategoryKey', $chartKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function byCategoryLabel($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byCategoryLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @return array
     */
    public function summaryByCategoryLabel($chartKey)
    {
        $res = $this->client->get(self::summaryReportUrl('byCategoryLabel', $chartKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    /**
     * @param $chartKey string
     * @param $objectType string
     * @return array
     */
    public function bySection($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('bySection', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @return array
     */
    public function summaryBySection($chartKey)
    {
        $res = $this->client->get(self::summaryReportUrl('bySection', $chartKey));
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    private static function reportUrl($reportType, $eventKey)
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
    }

    private static function summaryReportUrl($reportType, $chartKey)
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}/summary', array("key" => $chartKey, "reportType" => $reportType));
    }

    private function getChartReport($reportType, $chartKey, $bookWholeTables)
    {
        $res = $this->client->get(self::reportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables]]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json);
    }

    private static function mapMultiValuedReport($json)
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $status => $reportItems) {
            $result[$status] = $mapper->mapArray($reportItems, array(), ChartObjectInfo::class);
        }
        return $result;
    }

}
