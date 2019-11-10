<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOption\{Some, None, Option};

use App\RemoteServices\RawToken;
use App\RemoteServices\TokenContainer;

/**
* Testing full functionality of the abstract TokenContainer, which takes care of storing and refreshing token-like objects.
*/
class TokenContainerTest extends TestCase
{
    /**
    * The access token should be retrievable as long as it's valid.
    */
    public function test_retrieveValidToken() {
        $tokenContainer = new ValidTestTokenContainer();
        $token = $tokenContainer->getToken();
        $this->assertNotEmpty($token->get());
        $this->assertGreaterThan(0, $tokenContainer->getExpiresAt());
        $this->assertEquals(1, $tokenContainer->retrieveCallCount);
        $this->assertFalse($tokenContainer->hasSentMail);
    }

    /**
     * Access token should be refreshed if requested and is no longer valid.
     * This operation should not have a limit of calls.
     */
    public function test_refreshAccessToken()
    {
        $lastExpiry = -1;
        $callCount = 5;

        $tokenContainer = new RefreshTestTokenContainer();
        for ($i = 0; $i < $callCount; $i++) {
            // each time the token will be refreshed, and so the expiry time should change
            $this->assertNotEmpty($tokenContainer->getToken()->get());
            $this->assertGreaterThan($lastExpiry, $tokenContainer->getExpiresAt());
            $lastExpiry = $tokenContainer->getExpiresAt();
            sleep(1);
        }
        $this->assertEquals($callCount, $tokenContainer->retrieveCallCount);
    }

    /**
    * If access token retrieval fails, the refresh function should try agian, and return an empty (None) value if it fails after max retries.
    * There should also be a warning mail sent to notify developers of the issue.
    */
    public function test_refreshAccessTokenFails() {
        $maxAttempts = 3;
        $tokenContainer = new FailTestTokenContainer($maxAttempts);
        $this->assertInstanceOf(None::class, $tokenContainer->getToken());
        $this->assertEquals($maxAttempts, $tokenContainer->retrieveCallCount);
        $this->assertTrue($tokenContainer->hasSentMail);
    }
}

abstract class BaseTestTokenContainer extends TokenContainer {
    public $hasSentMail = false;

    protected function sendMailWarning($message) {
        $this->hasSentMail = true;
    }
}

class ValidTestTokenContainer extends BaseTestTokenContainer {
    public $retrieveCallCount = 0;

    protected function retrieveToken() {
        $this->retrieveCallCount += 1;
        $t = new class {
            public $access_token = 'valid_token';
            public $expires_in = 3600;
        };

        return Option::fromValue($t);
    }
}

class RefreshTestTokenContainer extends ValidTestTokenContainer {
    public function isTokenValid() {
        return false;
    }
}

class FailTestTokenContainer extends BaseTestTokenContainer {
    public $retrieveCallCount = 0;

    public function __construct($maxAttempts) {
        $this->MAX_ATTEMPTS = $maxAttempts;
        parent::__construct();
    }

    protected function retrieveToken() {
        $this->retrieveCallCount += 1;
        return None::create();
    }
}
