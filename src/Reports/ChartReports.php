<?php

namespace Seatsio\Reports;

use Seatsio\SeatsioJsonMapper;

class ChartReports
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
     * @return array
     */
    public function byLabel($chartKey)
    {
        return $this->getChartReport('byLabel', $chartKey);
    }

    /**
     * @param $chartKey string
     * @return array
     */
    public function byCategoryKey($chartKey)
    {
        return $this->getChartReport('byCategoryKey', $chartKey);
    }

    /**
     * @param $chartKey string
     * @return array
     */
    public function byCategoryLabel($chartKey)
    {
        return $this->getChartReport('byCategoryLabel', $chartKey);
    }

    private static function reportUrl($reportType, $eventKey)
    {
        return \GuzzleHttp\uri_template('/reports/charts/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
    }

    private function getChartReport($reportType, $chartKey)
    {
        $res = $this->client->get(self::reportUrl($reportType, $chartKey));
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
