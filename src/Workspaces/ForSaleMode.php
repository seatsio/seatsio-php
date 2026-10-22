<?php

namespace Seatsio\Workspaces;

enum ForSaleMode: string
{
    case RATE_LIMITED = 'RATE_LIMITED';
    case UNLIMITED_BILLABLE = 'UNLIMITED_BILLABLE';
}
