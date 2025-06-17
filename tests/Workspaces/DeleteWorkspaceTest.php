<?php

namespace Seatsio\Charts;

use Seatsio\SeatsioClientTest;
use Seatsio\SeatsioException;

class DeleteWorkspaceTest extends SeatsioClientTest
{
    public function testDeleteInactiveWorkspace()
    {
        $workspace = $this->seatsioClient->workspaces->create("a ws");
        $this->seatsioClient->workspaces->deactivate($workspace->key);

        $this->seatsioClient->workspaces->delete($workspace->key);

        try {
            $this->seatsioClient->workspaces->retrieve($workspace->key);
            throw new \Exception("Should have failed");
        } catch (SeatsioException $exception) {
            self::assertEquals(1, sizeof($exception->errors));
            self::assertEquals("No workspace found with public key '" . $workspace->key . "'", $exception->errors[0]->message);
        }
    }
}
