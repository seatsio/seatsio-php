<?php

namespace Seatsio\TicketBuyers;

use Seatsio\SeatsioClientTest;

class AddTicketBuyerIdsTest extends SeatsioClientTest
{
    public function testCanAddTicketBuyerIds()
    {
        $ticketBuyerId1 = self::uuid();
        $ticketBuyerId2 = self::uuid();
        $ticketBuyerId3 = self::uuid();

        $response = $this->seatsioClient->ticketBuyers->add([$ticketBuyerId1, $ticketBuyerId2, $ticketBuyerId3]);

        self::assertEqualsCanonicalizing([$ticketBuyerId1, $ticketBuyerId2, $ticketBuyerId3], $response->added);
        self::assertEmpty($response->alreadyPresent);
    }

    public function testCanAddTicketBuyerIds_listWithDuplicates()
    {
        $ticketBuyerId1 = self::uuid();
        $ticketBuyerId2 = self::uuid();

        $response = $this->seatsioClient->ticketBuyers->add([
            $ticketBuyerId1, $ticketBuyerId1, $ticketBuyerId1,
            $ticketBuyerId2, $ticketBuyerId2
        ]);

        self::assertEqualsCanonicalizing([$ticketBuyerId1, $ticketBuyerId2], $response->added);
        self::assertEmpty($response->alreadyPresent);
    }

    public function testCanAddTicketBuyerIds_sameIdDoesntGetAddedTwice()
    {
        $ticketBuyerId1 = self::uuid();
        $ticketBuyerId2 = self::uuid();

        $response = $this->seatsioClient->ticketBuyers->add([$ticketBuyerId1, $ticketBuyerId2]);

        self::assertEqualsCanonicalizing([$ticketBuyerId1, $ticketBuyerId2], $response->added);
        self::assertEmpty($response->alreadyPresent);

        $secondResponse = $this->seatsioClient->ticketBuyers->add([$ticketBuyerId1]);

        self::assertEquals([$ticketBuyerId1], $secondResponse->alreadyPresent);
    }

    public function testNullDoesNotGetAdded()
    {
        $ticketBuyerId1 = self::uuid();

        $response = $this->seatsioClient->ticketBuyers->add([$ticketBuyerId1, null]);

        self::assertEquals([$ticketBuyerId1], $response->added);
        self::assertEmpty($response->alreadyPresent);
    }
}
