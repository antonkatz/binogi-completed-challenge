<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\RemoteServices\SpotifyAuth;
use PHPOption\{Some, None};

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;


/**
* Unit tests of functions needed to retrieve the access token from Spotify.
*/
class SpotifyAuthTest extends TestCase
{
    /**
    * The Secret Key should be loaded successfully from a safe location.
    */
    public function test_loadSecretKey() {
        $key = SpotifyAuth::getSecretKey();
        $this->assertInstanceOf(Some::class, $key);
        $this->assertTrue(!empty($key->get()));
    }

    /**
    * The Client ID should be retrieved successfully, independent of any framework
    */
    public function test_getClientId() {
        // a shorthad approach: get of an empty Option will throw an error
        $this->assertTrue(!empty(SpotifyAuth::getClientId()->get()));
    }

    /**
     * Authorization header string should be generated.
     * Keep in mind, correctness can only be certain of after the access token is obtained.
     */
    public function test_generateAuthHeader()
    {
        $h = SpotifyAuth::generateAuthHeader();
        $this->assertStringStartsWith('Basic', $h);
        $this->assertTrue(
            // 10 is an arbitrary number simply to check that string contains more than 'Basic'
            Str::length($h) > 10
        );
    }

    /**
    * Access token should be retrieved successfully from Spotify API.
    */
    public function test_retrieveAccessToken() {
        $token = SpotifyAuth::retrieveAccessToken()->get();
        $this->assertNotEmpty($token->access_token);
        $this->assertGreaterThan(0, $token->expires_in);
    }

    /**
    * Access token retrieval should fail and a log message should indicate so.
    */
    public function test_retrieveAccessTokenFail() {
        $mock = new MockHandler([
            new Response(400), // reason is automatically assigned from code
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        SpotifyAuth::setClient($client);

        $token = SpotifyAuth::retrieveAccessToken();
        $this->assertInstanceOf(None::class, $token);

        // todo: test that logging indeed occurs (appears to be non-trivial)
    }
}
