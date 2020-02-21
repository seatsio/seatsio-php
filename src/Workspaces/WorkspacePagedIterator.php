<?php

namespace Seatsio\Workspaces;

use Seatsio\PagedIterator;

class WorkspacePagedIterator extends PagedIterator
{

    /**
     * @return Workspace
     */
    public function current()
    {
        return parent::current();
    }

}
