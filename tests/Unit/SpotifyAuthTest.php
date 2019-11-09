<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\RemoteServices\Spotify;
use PHPOption\{Some};

class SpotifyAuthTest extends TestCase
{
    /**
    * The Secret Key should be loaded successfully from a safe location.
    */
    public function test_loadSecretKey() {
        $key = Spotify::getSecretKey();
        $this->assertInstanceOf(Some::class, $key);
        $this->assertTrue(!empty($key.get()));
    }

    /**
    * The Client ID should be retrieved successfully, independent of any framework
    */
    public function test_getClientId() {
        // a shorthad approach: get of an empty Option will throw an error
        $this->assertTrue(!empty(Spotify::getClientId()->get()));
    }

    /**
     * Authorization header string should be generated correctly.
     */
    public function generateAuthHeaderTest()
    {

    }

    /**
    * Access token should be retrieved successfully from Spotify API.
    */
    public function retrieveAccessTokenTest() {

    }
}
