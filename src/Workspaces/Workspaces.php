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
     * @return string
     */
    public function regenerateSecretKey($key)
    {
        $res = $this->client->post(\GuzzleHttp\uri_template('/workspaces/{key}/actions/regenerate-secret-key', array("key" => $key)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $json->secretKey;
    }

    /**
     * @param $key string
     */
    public function activate($key)
    {
        $this->client->post(\GuzzleHttp\uri_template('/workspaces/{key}/actions/activate', array("key" => $key)));
    }

    /**
     * @param $key string
     */
    public function deactivate($key)
    {
        $this->client->post(\GuzzleHttp\uri_template('/workspaces/{key}/actions/deactivate', array("key" => $key)));
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
     * @param $filter string
     * @return WorkspacePagedIterator
     */
    public function listAll($filter = null)
    {
        return $this->iterator()->all($this->filterToArray($filter));
    }

    /**
     * @param $pageSize int
     * @param $filter string
     * @return WorkspacePage
     */
    public function listFirstPage($pageSize = null, $filter = null)
    {
        return $this->iterator()->firstPage($pageSize, $this->filterToArray($filter));
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @param $filter string
     * @return WorkspacePage
     */
    public function listPageAfter($afterId, $pageSize = null, $filter = null)
    {
        return $this->iterator()->pageAfter($afterId, $pageSize, $this->filterToArray($filter));
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @param $filter string
     * @return WorkspacePage
     */
    public function listPageBefore($beforeId, $pageSize = null, $filter = null)
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize, $this->filterToArray($filter));
    }

    /**
     * @return FilterableWorkspaceLister
     */
    private function iterator()
    {
        return new FilterableWorkspaceLister(new PageFetcher('/workspaces', $this->client, function () {
            return new WorkspacePage();
        }));
    }

    private function filterToArray($filter)
    {
        $result = [];
        if ($filter !== null) {
            $result['filter'] = $filter;
        }
        return $result;
    }
}
