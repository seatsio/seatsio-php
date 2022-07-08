<?php

namespace Seatsio\Reports\Charts;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use Seatsio\SeatsioJsonMapper;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\Charts\ChartObjectInfo;
use function Symfony\Component\String\b;

class ChartReports
{

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byLabel(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartReport('byLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byObjectType(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartReport('byObjectType', $chartKey, $bookWholeTables);
    }

    public function summaryByObjectType(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartSummaryReport('byObjectType', $chartKey, $bookWholeTables);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byCategoryKey(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartReport('byCategoryKey', $chartKey, $bookWholeTables);
    }

    public function summaryByCategoryKey(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartSummaryReport('byCategoryKey', $chartKey, $bookWholeTables);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byCategoryLabel(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartReport('byCategoryLabel', $chartKey, $bookWholeTables);
    }

    public function summaryByCategoryLabel(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartSummaryReport('byCategoryLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function bySection(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartReport('bySection', $chartKey, $bookWholeTables);
    }

    public function summaryBySection(string $chartKey, string $bookWholeTables = null): array
    {
        return $this->getChartSummaryReport('bySection', $chartKey, $bookWholeTables);
    }

    private static function reportUrl(string $reportType, string $chartKey): string
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}', array("key" => $chartKey, "reportType" => $reportType));
    }

    private static function summaryReportUrl($reportType, $chartKey)
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}/summary', array("key" => $chartKey, "reportType" => $reportType));
    }

    /**
     * @return ChartObjectInfo[][]
     */
    private function getChartReport(string $reportType, string $chartKey, ?string $bookWholeTables): array
    {
        $res = $this->client->get(self::reportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables]]);
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json);
    }

    private function getChartSummaryReport(string $reportType, string $chartKey, ?string $bookWholeTables): array
    {
        $res = $this->client->get(self::summaryReportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables]]);
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    private static function mapMultiValuedReport($json): array
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $status => $reportItems) {
            $result[$status] = $mapper->mapArray($reportItems, array(), ChartObjectInfo::class);
        }
        return $result;
    }

}
