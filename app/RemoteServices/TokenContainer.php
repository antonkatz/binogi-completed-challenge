<?php

namespace App\RemoteServices;

use \PhpOption\{None, Some, Option};

/**
* A generalizable container for string values that have an expiry time.
* Manages retrieval and refreshing. In case of failure sends an email notification.
* The retrieval and mail function must be implemented for sepcific services.
*/
abstract class TokenContainer {
    private $accessToken;
    /** in seconds */
    private $expiresAt = -1;

    /**
    * Sets maximum number of attempts to retrive a token.
    * Could be overwritten by extending class (eg. loaded from config).
    */
    protected $MAX_ATTEMPTS = 2;

    public function __construct() {
        $this->accessToken = None::create();
    }

    /**
    * Retrives the token. Must be specific to a service.
    * @return Option containing an object with fields: `access_token`, `token_type`, `expires_in`
    */
    abstract protected function retrieveToken();

    /**
    * Sends an email when max attempts is reached.
    * @param message string that will be sent as the body of an email,
    *                which should be augmented by the concrete implementation
    *                to indicate other relevant details (eg. service name).
    */
    abstract protected function sendMailWarning($message);

    /**
    * Retrives a token by trying up to the attempts limit, and sends a warning email if the limit is reached.
    */
    private function refreshToken() {
        for ($i = 0; $i < $this->MAX_ATTEMPTS; $i++) {
            $opt = $this->retrieveToken();
            if ($opt instanceof Some) {
                $raw = $opt->get();
                $obj = new \stdClass();
                $obj->token = $raw->access_token;
                $obj->type = $raw->token_type;

                $this->accessToken = Option::fromValue($obj);
                // -1 second is used as a safety buffer
                $this->expiresAt = ($raw->expires_in - 1) + time();
                return;
            }
        }
        $this->accessToken = None::create();
        $this->expiresAt = -1;
        $this->sendMailWarning('The token could be not retrieved before max attempts were reached');
    }

    /**
    * @return boolean true if the token is not expired and has a value, false otherwise.
    */
    public function isTokenValid() {
        $isNotExpired = $this->expiresAt > time();
        $isFilled = $this->accessToken instanceof Some;
        return $isNotExpired;
    }

    /**
    * @return Option access token object with fields `token` and `type` wrapped in an option,
    *                or an empty option if token is not valid
    */
    public function getToken() {
        if (!$this->isTokenValid()) {
            $this->refreshToken();
        }
        return $this->accessToken;
    }

    /**
    * @return integer Unix timestamp in seconds of when the token expires
    */
    public function getExpiresAt() {
        return $this->expiresAt;
    }
}
