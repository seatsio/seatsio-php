<?php

namespace Seatsio\Workspaces;

use Seatsio\PagedIterator;

class WorkspacePagedIterator extends PagedIterator
{

    public function current(): Workspace
    {
        return parent::current();
    }

}
