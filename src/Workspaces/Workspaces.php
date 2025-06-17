<?php

namespace Seatsio\Workspaces;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\GuzzleResponseDecoder;
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

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->active = new FilterableWorkspaceLister(new PageFetcher('/workspaces/active', $this->client, function () {
            return new WorkspacePage();
        }));
        $this->inactive = new FilterableWorkspaceLister(new PageFetcher('/workspaces/inactive', $this->client, function () {
            return new WorkspacePage();
        }));
    }

    public function create(string $name, bool $isTest = null): Workspace
    {
        $request = new stdClass();
        $request->name = $name;
        if ($isTest !== null) {
            $request->isTest = $isTest;
        }
        $res = $this->client->post('/workspaces', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Workspace());
    }

    public function update(string $key, string $name): void
    {
        $request = new stdClass();
        $request->name = $name;
        $this->client->post(UriTemplate::expand('/workspaces/{key}', array("key" => $key)), ['json' => $request]);
    }

    public function regenerateSecretKey(string $key): string
    {
        $res = $this->client->post(UriTemplate::expand('/workspaces/{key}/actions/regenerate-secret-key', array("key" => $key)));
        return GuzzleResponseDecoder::decodeToObject($res)->secretKey;
    }

    public function activate(string $key): void
    {
        $this->client->post(UriTemplate::expand('/workspaces/{key}/actions/activate', array("key" => $key)));
    }

    public function deactivate(string $key): void
    {
        $this->client->post(UriTemplate::expand('/workspaces/{key}/actions/deactivate', array("key" => $key)));
    }

    public function delete(string $key): void
    {
        $this->client->delete(UriTemplate::expand('/workspaces/{key}', array("key" => $key)));
    }

    public function setDefault(string $key): void
    {
        $this->client->post(UriTemplate::expand('/workspaces/actions/set-default/{key}', array("key" => $key)));
    }

    public function retrieve(string $key): Workspace
    {
        $res = $this->client->get(UriTemplate::expand('/workspaces/{key}', array("key" => $key)));
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Workspace());
    }

    public function listAll(string $filter = null): WorkspacePagedIterator
    {
        return $this->iterator()->all($filter);
    }

    public function listFirstPage(int $pageSize = null, string $filter = null): WorkspacePage
    {
        return $this->iterator()->firstPage($pageSize, $filter);
    }

    public function listPageAfter(int $afterId, int $pageSize = null, string $filter = null): WorkspacePage
    {
        return $this->iterator()->pageAfter($afterId, $pageSize, $filter);
    }

    public function listPageBefore(int $beforeId, int $pageSize = null, string $filter = null): WorkspacePage
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize, $filter);
    }

    private function iterator(): FilterableWorkspaceLister
    {
        return new FilterableWorkspaceLister(new PageFetcher('/workspaces', $this->client, function () {
            return new WorkspacePage();
        }));
    }
}
