<?php

namespace Seatsio\Workspaces;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class Workspaces
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var FilterableWorkspaceLister
     */
    public $active;
    public $inactive;

    public function __construct($client)
    {
        $this->client = $client;
        $this->active = new FilterableWorkspaceLister(new PageFetcher('/workspaces/active', $this->client, function () {
            return new WorkspacePage();
        }));
        $this->inactive = new FilterableWorkspaceLister(new PageFetcher('/workspaces/inactive', $this->client, function () {
            return new WorkspacePage();
        }));
    }

    /**
     * @param $name string
     * @param $isTest boolean
     * @return Workspace
     */
    public function create($name, $isTest = null)
    {
        $request = new stdClass();
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
        $request = new stdClass();
        $request->name = $name;
        $this->client->post(UriTemplate::expand('/workspaces/{key}', array("key" => $key)), ['json' => $request]);
    }

    /**
     * @param $key string
     * @return string
     */
    public function regenerateSecretKey($key)
    {
        $res = $this->client->post(UriTemplate::expand('/workspaces/{key}/actions/regenerate-secret-key', array("key" => $key)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $json->secretKey;
    }

    /**
     * @param $key string
     */
    public function activate($key)
    {
        $this->client->post(UriTemplate::expand('/workspaces/{key}/actions/activate', array("key" => $key)));
    }

    /**
     * @param $key string
     */
    public function deactivate($key)
    {
        $this->client->post(UriTemplate::expand('/workspaces/{key}/actions/deactivate', array("key" => $key)));
    }

    /**
     * @param $key string
     */
    public function setDefault($key)
    {
        $this->client->post(UriTemplate::expand('/workspaces/actions/set-default/{key}', array("key" => $key)));
    }

    /**
     * @param $key string
     * @return Workspace
     */
    public function retrieve($key)
    {
        $res = $this->client->get(UriTemplate::expand('/workspaces/{key}', array("key" => $key)));
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
        return $this->iterator()->all($filter);
    }

    /**
     * @param $pageSize int
     * @param $filter string
     * @return WorkspacePage
     */
    public function listFirstPage($pageSize = null, $filter = null)
    {
        return $this->iterator()->firstPage($pageSize, $filter);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @param $filter string
     * @return WorkspacePage
     */
    public function listPageAfter($afterId, $pageSize = null, $filter = null)
    {
        return $this->iterator()->pageAfter($afterId, $pageSize, $filter);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @param $filter string
     * @return WorkspacePage
     */
    public function listPageBefore($beforeId, $pageSize = null, $filter = null)
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize, $filter);
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
}
