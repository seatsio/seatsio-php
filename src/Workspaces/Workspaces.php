<?php

namespace Seatsio\Workspaces;

use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;

class Workspaces
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
     * @param $name string
     * @param $isTest boolean
     * @return Workspace
     */
    public function create($name, $isTest = null)
    {
        $request = new \stdClass();
        $request->name = $name;
        if ($isTest !== null) {
            $request->isTest = $isTest;
        }
        $res = $this->client->post('/workspaces', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Workspace());
    }

    /**
     * @param $key string
     * @param $name string
     * @return void
     */
    public function update($key, $name)
    {
        $request = new \stdClass();
        $request->name = $name;
        $this->client->post(\GuzzleHttp\uri_template('/workspaces/{key}', array("key" => $key)), ['json' => $request]);
    }

    /**
     * @param $key string
     * @return Workspace
     */
    public function retrieve($key)
    {
        $res = $this->client->get(\GuzzleHttp\uri_template('/workspaces/{key}', array("key" => $key)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Workspace());
    }

    /**
     * @return WorkspacePagedIterator
     */
    public function listAll()
    {
        return $this->iterator()->all();
    }

    /**
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function listFirstPage($pageSize = null)
    {
        return $this->iterator()->firstPage($pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function listPageAfter($afterId, $pageSize = null)
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function listPageBefore($beforeId, $pageSize = null)
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    /**
     * @return WorkspaceLister
     */
    private function iterator()
    {
        return new WorkspaceLister(new PageFetcher('/workspaces', $this->client, function () {
            return new WorkspacePage();
        }));
    }

}
