<?php

namespace Seatsio\Workspaces;

use Seatsio\PageFetcher;

class FilterableWorkspaceLister
{

    protected $pageFetcher;

    public function __construct(PageFetcher $pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    public function all(?string $filter = null): WorkspacePagedIterator
    {
        return new WorkspacePagedIterator($this->pageFetcher, $this->filterToArray($filter));
    }

    public function firstPage(?int $pageSize = null, ?string $filter = null): WorkspacePage
    {
        return $this->pageFetcher->fetchAfter(null, $this->filterToArray($filter), $pageSize);
    }

    public function pageAfter(int $afterId, ?int $pageSize = null, ?string $filter = null): WorkspacePage
    {
        return $this->pageFetcher->fetchAfter($afterId, $this->filterToArray($filter), $pageSize);
    }

    public function pageBefore(int $beforeId, ?int $pageSize = null, ?string $filter = null): WorkspacePage
    {
        return $this->pageFetcher->fetchBefore($beforeId, $this->filterToArray($filter), $pageSize);
    }

    private function filterToArray(?string $filter): array
    {
        $result = [];
        if ($filter !== null) {
            $result['filter'] = $filter;
        }
        return $result;
    }
}
