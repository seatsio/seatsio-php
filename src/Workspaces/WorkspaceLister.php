<?php

namespace Seatsio\Workspaces;

class WorkspaceLister
{

    protected $pageFetcher;

    public function __construct($pageFetcher)
    {
        $this->pageFetcher = $pageFetcher;
    }

    /**
     * @return WorkspacePagedIterator
     */
    public function all()
    {
        return new WorkspacePagedIterator($this->pageFetcher);
    }

    /**
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function firstPage($pageSize = null)
    {
        return $this->pageFetcher->fetchAfter(null, [], $pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function pageAfter($afterId, $pageSize = null)
    {
        return $this->pageFetcher->fetchAfter($afterId, [], $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return WorkspacePage
     */
    public function pageBefore($beforeId, $pageSize = null)
    {
        return $this->pageFetcher->fetchBefore($beforeId, [], $pageSize);
    }
}
