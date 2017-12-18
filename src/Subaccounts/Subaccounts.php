<?php

namespace Seatsio\Subaccounts;

use JsonMapper;
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
        if ($name) {
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
     * @return SubaccountLister
     */
    public function lister()
    {
        return new SubaccountLister(new PageFetcher('/subaccounts', $this->client, $this->pageSize, function () {
            return new SubaccountPage();
        }));
    }

}