<?php

namespace Seatsio\Reports\Charts;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\Charts\ChartObjectInfo;
use Seatsio\GuzzleResponseDecoder;
use Seatsio\SeatsioJsonMapper;

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
    public function byLabel(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartReport('byLabel', $chartKey, $bookWholeTables, $version);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byObjectType(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartReport('byObjectType', $chartKey, $bookWholeTables, $version);
    }

    public function summaryByObjectType(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartSummaryReport('byObjectType', $chartKey, $bookWholeTables, $version);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byCategoryKey(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartReport('byCategoryKey', $chartKey, $bookWholeTables, $version);
    }

    public function summaryByCategoryKey(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartSummaryReport('byCategoryKey', $chartKey, $bookWholeTables, $version);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byCategoryLabel(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartReport('byCategoryLabel', $chartKey, $bookWholeTables, $version);
    }

    public function summaryByCategoryLabel(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartSummaryReport('byCategoryLabel', $chartKey, $bookWholeTables, $version);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function bySection(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartReport('bySection', $chartKey, $bookWholeTables, $version);
    }

    public function summaryBySection(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartSummaryReport('bySection', $chartKey, $bookWholeTables, $version);
    }

    /**
     * @return ChartObjectInfo[][]
     */
    public function byZone(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartReport('byZone', $chartKey, $bookWholeTables, $version);
    }

    public function summaryByZone(string $chartKey, string $bookWholeTables = null, string $version = null): array
    {
        return $this->getChartSummaryReport('byZone', $chartKey, $bookWholeTables, $version);
    }

    private static function reportUrl(string $reportType, string $chartKey): string
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}', array("key" => $chartKey, "reportType" => $reportType));
    }

    private static function summaryReportUrl($reportType, $chartKey): string
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}/summary', array("key" => $chartKey, "reportType" => $reportType));
    }

    /**
     * @return ChartObjectInfo[][]
     */
    private function getChartReport(string $reportType, string $chartKey, ?string $bookWholeTables, ?string $version): array
    {
        $res = $this->client->get(self::reportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables, "version" => $version]]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        return $this->mapMultiValuedReport($json);
    }

    private function getChartSummaryReport(string $reportType, string $chartKey, ?string $bookWholeTables, ?string $version): array
    {
        $res = $this->client->get(self::summaryReportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables, "version" => $version]]);
        return GuzzleResponseDecoder::decodeToArray($res);
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
