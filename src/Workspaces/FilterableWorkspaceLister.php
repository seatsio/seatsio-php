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
     * @param $queryParams array
     * @return WorkspacePagedIterator
     */
    public function all($queryParams)
    {
        return new WorkspacePagedIterator($this->pageFetcher, $queryParams);
    }

    /**
     * @param $pageSize int
     * @param $queryParams array
     * @return WorkspacePage
     */
    public function firstPage($pageSize = null, $queryParams)
    {
        return $this->pageFetcher->fetchAfter(null, $queryParams, $pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @param $queryParams array
     * @return WorkspacePage
     */
    public function pageAfter($afterId, $pageSize = null, $queryParams)
    {
        return $this->pageFetcher->fetchAfter($afterId, $queryParams, $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @param $queryParams array
     * @return WorkspacePage
     */
    public function pageBefore($beforeId, $pageSize = null, $queryParams)
    {
        return $this->pageFetcher->fetchBefore($beforeId, $queryParams, $pageSize);
    }
}
