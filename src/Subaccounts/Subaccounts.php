<?php

namespace Seatsio\Subaccounts;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use Seatsio\Charts\Chart;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class Subaccounts
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var SubaccountLister
     */
    public $active;

    /**
     * @var SubaccountLister
     */
    public $inactive;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->active = new SubaccountLister(new PageFetcher('/subaccounts/active', $this->client, function () {
            return new SubaccountPage();
        }));
        $this->inactive = new SubaccountLister(new PageFetcher('/subaccounts/inactive', $this->client, function () {
            return new SubaccountPage();
        }));
    }

    public function retrieve(int $id): Subaccount
    {
        $res = $this->client->get('/subaccounts/' . $id);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Subaccount());
    }

    public function create(string $name = null): Subaccount
    {
        $request = new stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        $res = $this->client->post('/subaccounts', ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Subaccount());
    }

    public function update(int $id, string $name = null): void
    {
        $request = new stdClass();
        if ($name != null) {
            $request->name = $name;
        }
        $this->client->post('/subaccounts/' . $id, ['json' => $request]);
    }

    public function activate(int $id): void
    {
        $this->client->post('/subaccounts/' . $id . '/actions/activate');
    }

    public function deactivate(int $id): void
    {
        $this->client->post('/subaccounts/' . $id . '/actions/deactivate');
    }

    public function regenerateSecretKey(int $id): string
    {
        $res = $this->client->post('/subaccounts/' . $id . '/secret-key/actions/regenerate');
        $json = Utils::jsonDecode($res->getBody());
        return $json->secretKey;
    }

    public function regenerateDesignerKey(int $id): string
    {
        $res = $this->client->post('/subaccounts/' . $id . '/designer-key/actions/regenerate');
        $json = Utils::jsonDecode($res->getBody());
        return $json->designerKey;
    }

    public function copyChartToParent(int $id, string $chartKey): Chart
    {
        $res = $this->client->post('/subaccounts/' . $id . '/charts/' . $chartKey . '/actions/copy-to/parent');
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function copyChartToSubaccount(int $fromId, int $toId, string $chartKey): Chart
    {
        $res = $this->client->post('/subaccounts/' . $fromId . '/charts/' . $chartKey . '/actions/copy-to/' . $toId);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function listAll(SubaccountListParams $subaccountListParams = null): SubaccountPagedIterator
    {
        return $this->iterator()->all($this->listParamsToArray($subaccountListParams));
    }

    public function listFirstPage(int $pageSize = null, SubaccountListParams $subaccountListParams = null): SubaccountPage
    {
        return $this->iterator()->firstPage($this->listParamsToArray($subaccountListParams), $pageSize);
    }

    public function listPageAfter(int $afterId, int $pageSize = null, SubaccountListParams $subaccountListParams = null): SubaccountPage
    {
        return $this->iterator()->pageAfter($afterId, $this->listParamsToArray($subaccountListParams), $pageSize);
    }

    public function listPageBefore(int $beforeId, int $pageSize = null, SubaccountListParams $subaccountListParams = null): SubaccountPage
    {
        return $this->iterator()->pageBefore($beforeId, $this->listParamsToArray($subaccountListParams), $pageSize);
    }

    private function iterator(): FilterableSubaccountLister
    {
        return new FilterableSubaccountLister(new PageFetcher('/subaccounts', $this->client, function () {
            return new SubaccountPage();
        }));
    }

    private function listParamsToArray(?SubaccountListParams $subaccountListParams): array
    {
        if ($subaccountListParams == null) {
            return [];
        }
        return $subaccountListParams->toArray();
    }

}
