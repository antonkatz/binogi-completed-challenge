<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
        $this->assertTrue(!empty($key->get()));
    }

    /**
    * The Client ID should be retrieved successfully, independent of any framework
    */
    public function test_getClientId() {
        // a shorthad approach: get of an empty Option will throw an error
        $this->assertTrue(!empty(Spotify::getClientId()->get()));
    }

    /**
     * Authorization header string should be generated.
     * Keep in mind, correctness can only be certain of after the access token is obtained.
     */
    public function test_generateAuthHeader()
    {
        $h = Spotify::generateAuthHeader();
        $this->assertStringStartsWith('Basic', $h);
        $this->assertTrue(
            // 10 is an arbitrary number simply to check that string contains more than 'Basic'
            Str::length($h) > 10
        );
    }

    /**
    * Access token should be retrieved successfully from Spotify API.
    */
    public function retrieveAccessTokenTest() {

    }
}
