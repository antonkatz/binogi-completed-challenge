<?php

namespace App\RemoteServices;

use \GuzzleHttp\Client;

/**
* Used in classes that need to make HTTP request through a singleton of the Guzzle client.
*/
trait WithClientTrait {
    private static $client;
    private static $defaultHeaders;
    private static $defaultsChanged = false;

    /**
    * Creates a new client if none exists or if the defualts have changed.
    * @return Client existing or new singleton instance of the Guzzle client
    */
    private static function getClient() {
        if (self::$client == null || self::$defaultsChanged) {
            $options = [];
            if (isset(self::$defaultHeaders)) {
                $options['headers'] = self::$defaultHeaders;
            }
            self::$client = new Client($options);
            self::$defaultsChanged = false;
        }
        return self::$client;
    }

    private static function setClientDefaults($headers) {
        self::$defaultHeaders = $headers;
        self::$defaultsChanged = true;
    }

    /**
    * The main use of this function is to allow mocking in tests.
    */
    public static function setClient(Client $client) {
        self::$client = $client;
    }

}
