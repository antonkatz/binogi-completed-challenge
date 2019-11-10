<?php

namespace App\RemoteServices;

use Illuminate\Support\Facades\Log;
use \PHPOption\{Option, None};
use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request;

/**
* Methods for retrieving the access token from Spotify.
*/
class SpotifyAuth {
    private static $client = null;

    private static function getClient() {
        if (self::$client == null) {
            self::$client = new Client();
        }
        return self::$client;
    }

    /**
    * The main use of this function is to allow mocking in tests.
    */
    public static function setClient(Client $client) {
        self::$client = $client;
    }

    /**
    * @return Option filled with string.
    */
    public static function getSecretKey() {
        // the purpose of wrapping the config call in this function is to facilitate
        // security. Storing secrets in environmental variables is ok, however there
        // are stronger methods with encryption.
        return Option::fromValue(config('services.spotify.secret'));
    }

    /**
    * @return Option filled with string.
    */
    public static function getClientId() {
        // the purpose of wrapping the config call in this function is to facilitate
        // easier transition from Laravel if needed
        return Option::fromValue(config('services.spotify.clientid'));
    }

    /**
    * @return string value for the Authorization header formatted as specified in Spotify docs.
    */
    public static function generateAuthHeader() {
        $secret = self::getSecretKey()->get();
        $id = self::getClientId() -> get();
        $b64 = base64_encode($id . ":" . $secret);
        return 'Basic ' . $b64;
    }

    /**
    * Sends a request to Spotify asking for the access token.
    * In case of an error logs it, but does not throw an exception.
    * @return Option of object in the JSON format from Spotify docs.
    */
    public static function retrieveAccessToken() {
        // todo: figure out if sending an async request will have a performance benefit
        // that requries understanding of how Laravel handles incoming requests, and if this function will be blocking
        // other incoming requests while the communication with Spotify API completes.

        $url = config('services.spotify.access-token-endpoint');
        $authHeader = self::generateAuthHeader();
        $req = new Request('POST', $url,
                [
                    'Authorization' => $authHeader,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'grant_type=client_credentials'
        );

        try {
            $completed = self::getClient()->send($req);
            $body = $completed->getBody();
            return Option::fromValue(json_decode($body));
        } catch (\Exception $e) {
            $resp = $e->getResponse();
            $msg = 'Request to retrive Spotify access token failed: ' .
                $resp->getStatusCode() . ' ' . $resp->getReasonPhrase();
            Log::error($msg);
            return None::create();
        }
        return null;
    }
}
