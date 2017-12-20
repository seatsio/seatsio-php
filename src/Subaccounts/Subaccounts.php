<?php

namespace Seatsio\Subaccounts;

use JsonMapper;
use Seatsio\Charts\Chart;
use Seatsio\PageFetcher;

class Subaccounts
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    private $pageSize;

    public function __construct($client, $pageSize)
    {
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    /**
     * @return Subaccount
     */
    public function retrieve($id)
    {
        $res = $this->client->request('GET', '/subaccounts/' . $id);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return Subaccount
     */
    public function create($name = null)
    {
        $request = new \stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        $res = $this->client->request('POST', '/subaccounts', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Subaccount());
    }

    /**
     * @return void
     */
    public function update($id, $name)
    {
        $request = new \stdClass();
        $request->name = $name;
        $this->client->request('POST', '/subaccounts/' . $id, ['json' => $request]);
    }

    /**
     * @return void
     */
    public function activate($id)
    {
        $this->client->request('POST', '/subaccounts/' . $id . '/actions/activate');
    }

    /**
     * @return void
     */
    public function deactivate($id)
    {
        $this->client->request('POST', '/subaccounts/' . $id . '/actions/deactivate');
    }

    /**
     * @return string
     */
    public function regenerateSecretKey($id)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $id . '/secret-key/actions/regenerate');
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $json->secretKey;
    }

    /**
     * @return string
     */
    public function regenerateDesignerKey($id)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $id . '/designer-key/actions/regenerate');
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $json->designerKey;
    }

    /**
     * @return Chart
     */
    public function copyChartToParent($id, $chartKey)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $id . '/charts/' . $chartKey . '/actions/copy-to/parent');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Chart());
    }

    /**
     * @return Chart
     */
    public function copyChartToSubaccount($fromId, $toId, $chartKey)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $fromId . '/charts/' . $chartKey . '/actions/copy-to/' . $toId);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Chart());
    }

    /**
     * @return SubaccountLister
     */
    public function lister()
    {
        return new SubaccountLister(new PageFetcher('/subaccounts', $this->client, $this->pageSize, function () {
            return new SubaccountPage();
        }));
    }

    /**
     * @return SubaccountLister
     */
    public function active()
    {
        return new SubaccountLister(new PageFetcher('/subaccounts/active', $this->client, $this->pageSize, function () {
            return new SubaccountPage();
        }));
    }

    /**
     * @return SubaccountLister
     */
    public function inactive()
    {
        return new SubaccountLister(new PageFetcher('/subaccounts/inactive', $this->client, $this->pageSize, function () {
            return new SubaccountPage();
        }));
    }

}