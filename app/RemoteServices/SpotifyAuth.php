<?php

namespace App\RemoteServices;

use Illuminate\Support\Facades\Log;
use App\RemoteServices\WithClientTrait;

use \PHPOption\{Option, None};
use \GuzzleHttp\Psr7\Request;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
* Methods for retrieving the access token from Spotify.
*/
class SpotifyAuth {
    use WithClientTrait;

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
        $secret = self::getSecretKey();
        $id = self::getClientId();
        if (($secret instanceof None) || ($id instanceof None)) {
            throw new \Exception('Missing Spotify secret or client id');
        }
        $b64 = base64_encode($id->get() . ":" . $secret->get());
        return 'Basic ' . $b64;
    }

    /**
    * Sends a request to Spotify asking for the access token.
    * In case of an error logs it, but does not throw an exception.
    * @return Option of object in the JSON format from Spotify docs containing `access_token`, `expires_in`, `token_type` values.
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
        } catch (ClientErrorResponseException $e) {
            $resp = $e->getResponse();
            $msg = 'Request to retrive Spotify access token failed: ' .
                $resp->getStatusCode() . ' ' . $resp->getReasonPhrase();
            Log::error($msg);
            return None::create();
        }
    }

    /**
    * Helper method for generating headers to be passed into an HTTP client.
    * @return array of authorization realted headers to be used in request to Spotify
    */
    public static function generateAccessTokenHeaders(TokenContainer $tokenContainer) {
        $token = $tokenContainer->getToken()->get();
        $authHeader = $token->type . ' ' . $token->token;
        return [
                    'Authorization' => $authHeader,
        ];
    }
}
