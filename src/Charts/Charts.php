<?php

namespace Seatsio\Charts;

use Psr\Http\Message\StreamInterface;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;

class Charts
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var ChartLister
     */
    public $archive;

    public function __construct($client)
    {
        $this->client = $client;
        $this->archive = new ChartLister(new PageFetcher('/charts/archive', $this->client, function () {
            return new ChartPage();
        }));
    }

    /**
     * @param $name string
     * @param $venueType string
     * @param $categories array|\Seatsio\Charts\Category[]
     * @return Chart
     */
    public function create($name = null, $venueType = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        if ($venueType !== null) {
            $request->venueType = $venueType;
        }
        if ($categories != null) {
            $request->categories = $categories;
        }
        $res = $this->client->post('/charts', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    /**
     * @param $key string
     * @param $name string
     * @param $categories array|\Seatsio\Charts\Category[]
     * @return void
     */
    public function update($key, $name = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post('/charts/' . $key, ['json' => $request]);
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function retrieve($key)
    {
        $res = $this->client->get('/charts/' . $key);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function retrieveWithEvents($key)
    {
        $res = $this->client->get('/charts/' . $key . '?expand=events');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    /**
     * @param $key string
     * @return object
     */
    public function retrievePublishedVersion($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return object
     */
    public function retrieveDraftVersion($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/draft');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return void
     */
    public function publishDraftVersion($key)
    {
        $this->client->post('/charts/' . $key . '/version/draft/actions/publish');
    }

    /**
     * @param $key string
     * @return void
     */
    public function discardDraftVersion($key)
    {
        $this->client->post('/charts/' . $key . '/version/draft/actions/discard');
    }

    /**
     * @param $key string
     * @return void
     */
    public function moveToArchive($key)
    {
        $this->client->post('/charts/' . $key . '/actions/move-to-archive');
    }

    /**
     * @param $key string
     * @return void
     */
    public function moveOutOfArchive($key)
    {
        $this->client->post('/charts/' . $key . '/actions/move-out-of-archive');
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function copy($key)
    {
        $res = $this->client->post('/charts/' . $key . '/version/published/actions/copy');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function copyDraftVersion($key)
    {
        $res = $this->client->post('/charts/' . $key . '/version/draft/actions/copy');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $chartKey string
     * @param $subaccountId int
     * @return Chart
     */
    public function copyToSubaccount($chartKey, $subaccountId)
    {
        $res = $this->client->post('/charts/' . $chartKey . '/version/published/actions/copy-to/' . $subaccountId);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $chartKey string
     * @param $toWorkspaceKey string
     * @return Chart
     */
    public function copyToWorkspace($chartKey, $toWorkspaceKey)
    {
        $res = $this->client->post('/charts/' . $chartKey . '/version/published/actions/copy-to-workspace/' . $toWorkspaceKey);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return StreamInterface
     */
    public function retrievePublishedVersionThumbnail($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/published/thumbnail');
        return $res->getBody();
    }

    /**
     * @param $key string
     * @return object
     */
    public function validatePublishedVersion($key)
    {
        $res = $this->client->post('/charts/' . $key . '/version/published/actions/validate');
         return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return object
     */
    public function validateDraftVersion($key)
    {
        $res = $this->client->post('/charts/' . $key . '/version/draft/actions/validate');
         return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return StreamInterface
     */
    public function retrieveDraftVersionThumbnail($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/draft/thumbnail');
        return $res->getBody();
    }

    /**
     * @return string[]
     */
    public function listAllTags()
    {
        $res = $this->client->get('/charts/tags');
        return \GuzzleHttp\json_decode($res->getBody())->tags;
    }

    /**
     * @param $key string
     * @param $tag string
     * @return void
     */
    public function addTag($key, $tag)
    {
        $this->client->post(\GuzzleHttp\uri_template('/charts/{key}/tags/{tag}', array("key" => $key, "tag" => $tag)));
    }

    /**
     * @param $key string
     * @param $tag string
     * @return void
     */
    public function removeTag($key, $tag)
    {
        $this->client->delete(\GuzzleHttp\uri_template('/charts/{key}/tags/{tag}', array("key" => $key, "tag" => $tag)));
    }

    /**
     * @param $chartListParams ChartListParams
     * @return ChartPagedIterator
     */
    public function listAll($chartListParams = null)
    {
        return $this->iterator()->all($this->listParamsToArray($chartListParams));
    }

    /**
     * @param $chartListParams ChartListParams
     * @param $pageSize int
     * @return ChartPage
     */
    public function listFirstPage($chartListParams = null, $pageSize = null)
    {
        return $this->iterator()->firstPage($this->listParamsToArray($chartListParams), $pageSize);
    }

    /**
     * @param $afterId int
     * @param $chartListParams ChartListParams
     * @param $pageSize int
     * @return ChartPage
     */
    public function listPageAfter($afterId, $chartListParams = null, $pageSize = null)
    {
        return $this->iterator()->pageAfter($afterId, $this->listParamsToArray($chartListParams), $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $chartListParams ChartListParams
     * @param $pageSize int
     * @return ChartPage
     */
    public function listPageBefore($beforeId, $chartListParams = null, $pageSize = null)
    {
        return $this->iterator()->pageBefore($beforeId, $this->listParamsToArray($chartListParams), $pageSize);
    }

    /**
     * @return FilterableChartLister
     */
    private function iterator()
    {
        return new FilterableChartLister(new PageFetcher('/charts', $this->client, function () {
            return new ChartPage();
        }));
    }

    private function listParamsToArray($chartListParams)
    {
        if ($chartListParams == null) {
            return [];
        }
        return $chartListParams->toArray();
    }

}
