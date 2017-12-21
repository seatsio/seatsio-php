<?php

namespace Seatsio\Subaccounts;

use Seatsio\Charts\Chart;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;

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
     * @var $id int
     * @return Subaccount
     */
    public function retrieve($id)
    {
        $res = $this->client->request('GET', '/subaccounts/' . $id);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @var $name string
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
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Subaccount());
    }

    /**
     * @var $id int
     * @var $name string
     * @return void
     */
    public function update($id, $name)
    {
        $request = new \stdClass();
        $request->name = $name;
        $this->client->request('POST', '/subaccounts/' . $id, ['json' => $request]);
    }

    /**
     * @var $id int
     * @return void
     */
    public function activate($id)
    {
        $this->client->request('POST', '/subaccounts/' . $id . '/actions/activate');
    }

    /**
     * @var $id int
     * @return void
     */
    public function deactivate($id)
    {
        $this->client->request('POST', '/subaccounts/' . $id . '/actions/deactivate');
    }

    /**
     * @var $id int
     * @return string
     */
    public function regenerateSecretKey($id)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $id . '/secret-key/actions/regenerate');
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $json->secretKey;
    }

    /**
     * @var $id int
     * @return string
     */
    public function regenerateDesignerKey($id)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $id . '/designer-key/actions/regenerate');
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $json->designerKey;
    }

    /**
     * @var $id int
     * @var $chartKey string
     * @return Chart
     */
    public function copyChartToParent($id, $chartKey)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $id . '/charts/' . $chartKey . '/actions/copy-to/parent');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    /**
     * @var $fromId int
     * @var $toId int
     * @var $chartKey string
     * @return Chart
     */
    public function copyChartToSubaccount($fromId, $toId, $chartKey)
    {
        $res = $this->client->request('POST', '/subaccounts/' . $fromId . '/charts/' . $chartKey . '/actions/copy-to/' . $toId);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
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