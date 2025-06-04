<?php

namespace Seatsio\TicketBuyers;

use Seatsio\SeatsioClientTest;

class ListTicketBuyerIdsTest extends SeatsioClientTest
{
    public function testCanListTicketBuyerIds(): void
    {
        $ticketBuyerId1 = self::uuid();
        $ticketBuyerId2 = self::uuid();
        $ticketBuyerId3 = self::uuid();

        $this->seatsioClient->ticketBuyers->add($ticketBuyerId1, $ticketBuyerId2, $ticketBuyerId3);

        $ticketBuyerIds = iterator_to_array($this->seatsioClient->ticketBuyers->listAll(), false);

        self::assertCount(3, $ticketBuyerIds);
        self::assertContains($ticketBuyerId1, $ticketBuyerIds);
        self::assertContains($ticketBuyerId2, $ticketBuyerIds);
        self::assertContains($ticketBuyerId3, $ticketBuyerIds);
    }
}
