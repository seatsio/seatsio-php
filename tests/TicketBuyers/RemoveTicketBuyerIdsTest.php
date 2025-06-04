<?php


namespace Seatsio\TicketBuyers;

use Seatsio\SeatsioClientTest;

class RemoveTicketBuyerIdsTest extends SeatsioClientTest
{
    public function testCanRemoveTicketBuyerIds(): void
    {
        $ticketBuyerId1 = self::uuid();
        $ticketBuyerId2 = self::uuid();
        $ticketBuyerId3 = self::uuid();
        $this->seatsioClient->ticketBuyers->add([$ticketBuyerId1, $ticketBuyerId2]);

        $removeResponse = $this->seatsioClient->ticketBuyers->remove([$ticketBuyerId1, $ticketBuyerId2, $ticketBuyerId3]);

        self::assertEqualsCanonicalizing([$ticketBuyerId1, $ticketBuyerId2], $removeResponse->removed);
        self::assertEqualsCanonicalizing([$ticketBuyerId3], $removeResponse->notPresent);
    }

    public function testNullDoesNotGetRemoved(): void
    {
        $ticketBuyerId1 = self::uuid();
        $ticketBuyerId2 = self::uuid();
        $ticketBuyerId3 = self::uuid();

        $this->seatsioClient->ticketBuyers->add([$ticketBuyerId1, $ticketBuyerId2, $ticketBuyerId3]);

        $response = $this->seatsioClient->ticketBuyers->remove([$ticketBuyerId1, null]);

        self::assertEquals([$ticketBuyerId1], $response->removed);
        self::assertEmpty($response->notPresent);
    }
}
