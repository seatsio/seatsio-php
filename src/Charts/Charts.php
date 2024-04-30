<?php

namespace Seatsio\Charts;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use GuzzleHttp\Utils;
use Psr\Http\Message\StreamInterface;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class Charts
{

    /**
     * @var Client
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

    public function create(string $name = null, string $venueType = null, array $categories = null): Chart
    {
        $request = new stdClass();
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
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function update(string $key, string $name = null, array $categories = null): void
    {
        $request = new stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post('/charts/' . $key, ['json' => $request]);
    }

    public function addCategory(string $key, CategoryRequestBuilder $category): void
    {
        $this->client->post('/charts/' . $key . '/categories', ['json' => $category]);
    }

    /**
     * @param string $key
     * @param $categoryKey string|int
     * @return void
     */
    public function removeCategory(string $key, $categoryKey): void
    {
        $this->client->delete('/charts/' . $key . '/categories/' . $categoryKey);
    }

    /**
     * @return Category[]
     */
    public function listCategories(string $chartKey): array
    {
        $res = $this->client->get('/charts/' . $chartKey . '/categories');
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return array_map(function($cat) {
            return new Category($cat->key, $cat->label, $cat->color, $cat->accessible);
        }, $json->categories);
    }

    /**
     * @param $chartKey string
     * @param $categoryKey string|int
     * @param $params CategoryUpdateParams
     * @return void
     */
    public function updateCategory(string $chartKey, $categoryKey, CategoryUpdateParams $params): void
    {
        $res = $this->client->post('/charts/' . $chartKey . '/categories/' . $categoryKey, ['json' => $params]);
    }

    public function retrieve(string $key): Chart
    {
        $res = $this->client->get('/charts/' . $key);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function retrieveWithEvents(string $key): Chart
    {
        $res = $this->client->get('/charts/' . $key . '?expand=events');
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function retrievePublishedVersion(string $key): object
    {
        $res = $this->client->get('/charts/' . $key . '/version/published');
        return Utils::jsonDecode($res->getBody());
    }

    public function retrieveDraftVersion(string $key): object
    {
        $res = $this->client->get('/charts/' . $key . '/version/draft');
        return Utils::jsonDecode($res->getBody());
    }

    public function publishDraftVersion(string $key): void
    {
        $this->client->post('/charts/' . $key . '/version/draft/actions/publish');
    }

    public function discardDraftVersion(string $key): void
    {
        $this->client->post('/charts/' . $key . '/version/draft/actions/discard');
    }

    public function moveToArchive(string $key): void
    {
        $this->client->post('/charts/' . $key . '/actions/move-to-archive');
    }

    public function moveOutOfArchive(string $key): void
    {
        $this->client->post('/charts/' . $key . '/actions/move-out-of-archive');
    }

    public function copy(string $key): Chart
    {
        $res = $this->client->post('/charts/' . $key . '/version/published/actions/copy');
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function copyDraftVersion(string $key): Chart
    {
        $res = $this->client->post('/charts/' . $key . '/version/draft/actions/copy');
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function copyToWorkspace(string $chartKey, string $toWorkspaceKey): Chart
    {
        $res = $this->client->post('/charts/' . $chartKey . '/version/published/actions/copy-to-workspace/' . $toWorkspaceKey);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function copyFromWorkspaceTo(string $chartKey, string $fromWorkspaceKey, string $toWorkspaceKey): Chart
    {
        $res = $this->client->post('/charts/' . $chartKey . '/version/published/actions/copy/from/' . $fromWorkspaceKey . '/to/' . $toWorkspaceKey);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    public function retrievePublishedVersionThumbnail(string $key): StreamInterface
    {
        $res = $this->client->get('/charts/' . $key . '/version/published/thumbnail');
        return $res->getBody();
    }

    public function validatePublishedVersion(string $key): object
    {
        $res = $this->client->post('/charts/' . $key . '/version/published/actions/validate');
        return Utils::jsonDecode($res->getBody());
    }

    public function validateDraftVersion(string $key): object
    {
        $res = $this->client->post('/charts/' . $key . '/version/draft/actions/validate');
        return Utils::jsonDecode($res->getBody());
    }

    public function retrieveDraftVersionThumbnail(string $key): StreamInterface
    {
        $res = $this->client->get('/charts/' . $key . '/version/draft/thumbnail');
        return $res->getBody();
    }

    /**
     * @return string[]
     */
    public function listAllTags(): array
    {
        $res = $this->client->get('/charts/tags');
        return Utils::jsonDecode($res->getBody())->tags;
    }

    public function addTag(string $key, string $tag): void
    {
        $this->client->post(UriTemplate::expand('/charts/{key}/tags/{tag}', array("key" => $key, "tag" => $tag)));
    }

    public function removeTag(string $key, string $tag): void
    {
        $this->client->delete(UriTemplate::expand('/charts/{key}/tags/{tag}', array("key" => $key, "tag" => $tag)));
    }

    public function listAll(ChartListParams $chartListParams = null): ChartPagedIterator
    {
        return $this->iterator()->all($this->listParamsToArray($chartListParams));
    }

    public function listFirstPage(ChartListParams $chartListParams = null, int $pageSize = null): ChartPage
    {
        return $this->iterator()->firstPage($this->listParamsToArray($chartListParams), $pageSize);
    }

    public function listPageAfter(int $afterId, ChartListParams $chartListParams = null, int $pageSize = null): ChartPage
    {
        return $this->iterator()->pageAfter($afterId, $this->listParamsToArray($chartListParams), $pageSize);
    }

    public function listPageBefore(int $beforeId, ChartListParams $chartListParams = null, int $pageSize = null): ChartPage
    {
        return $this->iterator()->pageBefore($beforeId, $this->listParamsToArray($chartListParams), $pageSize);
    }

    private function iterator(): FilterableChartLister
    {
        return new FilterableChartLister(new PageFetcher('/charts', $this->client, function () {
            return new ChartPage();
        }));
    }

    private function listParamsToArray($chartListParams): array
    {
        if ($chartListParams == null) {
            return [];
        }
        return $chartListParams->toArray();
    }

}
