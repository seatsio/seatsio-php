<?php

namespace Seatsio\Reports\Charts;

use GuzzleHttp\Client;
use Seatsio\SeatsioJsonMapper;
use GuzzleHttp\UriTemplate\UriTemplate;

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
     * @param $bookWholeTables string
     * @return array
     */
    public function byCategoryKey($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byCategoryKey', $chartKey, $bookWholeTables);
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

    private static function reportUrl($reportType, $eventKey)
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
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
            $result[$status] = $mapper->mapArray($reportItems, array(), ChartReportItem::class);
        }
        return $result;
    }

}
