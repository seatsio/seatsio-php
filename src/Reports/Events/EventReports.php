<?php

namespace Seatsio\Reports\Events;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use GuzzleHttp\Utils;
use Seatsio\Events\EventObjectInfo;
use Seatsio\SeatsioJsonMapper;

class EventReports
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
     * @return EventObjectInfo[][]
     */
    public function byStatus(string $eventKey, string $status = null): array
    {
        $res = $this->client->get(self::reportUrl('byStatus', $eventKey, $status));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $status);
    }

    public function summaryByStatus(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byStatus', $eventKey));
        $json = Utils::jsonDecode($res->getBody(), true);
        return $json;
    }

    public function deepSummaryByStatus(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byStatus', $eventKey));
        $json = Utils::jsonDecode($res->getBody(), true);
        return $json;
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byObjectType(string $eventKey, string $objectType = null): array
    {
        $res = $this->client->get(self::reportUrl('byObjectType', $eventKey, $objectType));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $objectType);
    }

    public function summaryByObjectType(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byObjectType', $eventKey));
        $json = Utils::jsonDecode($res->getBody(), true);
        return $json;
    }

    public function deepSummaryByObjectType(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byObjectType', $eventKey));
        $json = Utils::jsonDecode($res->getBody(), true);
        return $json;
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byCategoryLabel(string $eventKey, string $categoryLabel = null): array
    {
        $res = $this->client->get(self::reportUrl('byCategoryLabel', $eventKey, $categoryLabel));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $categoryLabel);
    }

    public function summaryByCategoryLabel(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byCategoryLabel', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    public function deepSummaryByCategoryLabel(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byCategoryLabel', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byCategoryKey(string $eventKey, string $categoryKey = null): array
    {
        $res = $this->client->get(self::reportUrl('byCategoryKey', $eventKey, $categoryKey));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $categoryKey);
    }

    public function summaryByCategoryKey(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byCategoryKey', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    public function deepSummaryByCategoryKey(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byCategoryKey', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byLabel(string $eventKey, string $label = null): array
    {
        $res = $this->client->get(self::reportUrl('byLabel', $eventKey, $label));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $label);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byOrderId(string $eventKey, string $orderId = null): array
    {
        $res = $this->client->get(self::reportUrl('byOrderId', $eventKey, $orderId));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $orderId);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function bySection(string $eventKey, string $section = null): array
    {
        $res = $this->client->get(self::reportUrl('bySection', $eventKey, $section));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $section);
    }

    public function summaryBySection(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('bySection', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    public function deepSummaryBySection(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('bySection', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byChannel(string $eventKey, string $channel = null): array
    {
        $res = $this->client->get(self::reportUrl('byChannel', $eventKey, $channel));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $channel);
    }

    public function summaryByChannel(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byChannel', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    public function deepSummaryByChannel(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byChannel', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byAvailability(string $eventKey, string $selectability = null): array
    {
        $res = $this->client->get(self::reportUrl('byAvailability', $eventKey, $selectability));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $selectability);
    }

    /**
     * @return EventObjectInfo[][]
     */
    public function byAvailabilityReason(string $eventKey, string $availabilityReason = null): array
    {
        $res = $this->client->get(self::reportUrl('byAvailabilityReason', $eventKey, $availabilityReason));
        $json = Utils::jsonDecode($res->getBody());
        return $this->mapMultiValuedReport($json, $availabilityReason);
    }

    /**
     * @return array
     */
    public function summaryByAvailability(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byAvailability', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @return array
     */
    public function summaryByAvailabilityReason(string $eventKey): array
    {
        $res = $this->client->get(self::summaryReportUrl('byAvailabilityReason', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    public function deepSummaryByAvailability(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byAvailability', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    public function deepSummaryByAvailabilityReason(string $eventKey): array
    {
        $res = $this->client->get(self::deepSummaryReportUrl('byAvailabilityReason', $eventKey));
        return Utils::jsonDecode($res->getBody(), true);
    }

    /**
     * @param $json mixed
     * @return EventObjectInfo[][]
     */
    private static function mapMultiValuedReport($json, ?string $filter): array
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $key => $reportItems) {
            $result[$key] = $mapper->mapArray($reportItems, array(), EventObjectInfo::class);
        }
        if ($filter === null || is_array($filter)) {
            return $result;
        }
        if (array_key_exists($filter, $result)) {
            return $result[$filter];
        }
        return [];
    }

    private static function reportUrl(string $reportType, string $eventKey, ?string $filter): string
    {
        if ($filter === null) {
            return UriTemplate::expand('/reports/events/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
        }
        return UriTemplate::expand('/reports/events/{key}/{reportType}/{filter}', array("key" => $eventKey, "reportType" => $reportType, "filter" => $filter));
    }

    private static function summaryReportUrl(string $reportType, string $eventKey): string
    {
        return UriTemplate::expand('/reports/events/{key}/{reportType}/summary', array("key" => $eventKey, "reportType" => $reportType));
    }

    private static function deepSummaryReportUrl(string $reportType, string $eventKey): string
    {
        return UriTemplate::expand('/reports/events/{key}/{reportType}/summary/deep', array("key" => $eventKey, "reportType" => $reportType));
    }

}
