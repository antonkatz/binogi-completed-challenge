<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpotifyAuthTest extends TestCase
{
    /**
    * The access token should be retrievable as long as it's valid.
    */
    public function retrieveValidTokenTest() {

    }

    /**
     * Access token should be refreshed if requested and is no longer valid.
     */
    public function refreshAccessTokenTest()
    {

    }

    /**
    * If access token retrieval fails, the refresh function should try agian, and return an empty (None) value if it fails again.
    */
    public function refreshAccessTokenFailsTest() {

    }
}
