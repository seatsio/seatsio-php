<?php

namespace Seatsio\Workspaces;

class FilterableWorkspaceLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @param $filter string
     * @return WorkspacePagedIterator
     */
    public function all($filter = null)
    {
        return new WorkspacePagedIterator($this->pageFetcher, $this->filterToArray($filter));
    }

    /**
     * @param $filter string
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function firstPage($pageSize = null, $filter = null)
    {
        return $this->pageFetcher->fetchAfter(null, $this->filterToArray($filter), $pageSize);
    }

    /**
     * @param $afterId int
     * @param $filter string
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function pageAfter($afterId, $pageSize = null, $filter = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, $this->filterToArray($filter), $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $filter string
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function pageBefore($beforeId, $pageSize = null, $filter = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, $this->filterToArray($filter), $pageSize);
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
