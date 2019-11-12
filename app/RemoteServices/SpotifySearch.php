<?php

namespace App\RemoteServices;

use \PhpOption\{Option, None};
use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request;

/**
* Used for performing paginated searches on Spotify given a search term and a search type (artist, album, track)
*/
class SpotifySearch {
    private static $ALLOWED_SEARCH_TYPES = ['album','artist','track'];

    private $tokenContainer;
    private $client;

    /** Url encoded */
    private $searchTerm;
    private $searchType;
    /** Could be made settable on initialization. */
    private $pageSize = 20;

    private $currentOffset;

    private $totalItems;
    private $currentPage;

    private function generateHeaders() {
        $token = $this->tokenContainer->getToken()->get();
        $authHeader = $token->type . ' ' . $token->token;
        return [
                    'Authorization' => $authHeader,
                    'Content-Type' => 'application/x-www-form-urlencoded'
        ];
    }

    private function generateQuery($offset) {
        $q = 'q='.$this->searchTerm;
        $type = 'type=' . $this->searchType;
        $o = 'offset=' . $offset;
        $l = 'limit='.$this->getPageSize();
        return implode('&', [$q, $type, $o, $l]);
    }

    private function sendRequest($offset) {
        $request = new Request('GET', '?' . $this->generateQuery($offset), $this->generateHeaders());
        $resp = $this->client->send($request);
        $body = json_decode($resp->getBody());
        // the returned results are be split under 'artists', 'tracks', etc.
        // we will only ever get one type
        $resultKey = array_keys(get_object_vars($body))[0];
        $result = $body->$resultKey;

        $this->totalItems = $result->total;
        $items = $result->items;
        return $items;
    }

    /**
    * Sets up search instance to be ready to retrieve results with `getNextPage()`
    * @param searchTerm string to perform the search on
    * @param searchType Spotify has sereval types of items that can be searched: artist, album, track
    */
    public function __construct($searchTerm, $searchType) {
        $baseUrl = config('services.spotify.api-base-url') . '/search';

        $this->client = new Client([
            'base_uri' => $baseUrl
        ]);
        $this->tokenContainer = new SpotifyTokenContainer();
        $this->searchTerm = urlEncode($searchTerm);
        if (!in_array($searchType, $this::$ALLOWED_SEARCH_TYPES)) {
            // todo. make exception a specific class
            throw new \Exception('"' . $searchType . '" is not withing allowed list');
        }
        $this->searchType = $searchType;
        $this->nextUrl = $this->generateQuery(0);
    }

    /**
    * @return int the max number of items returned per call
    */
    public function getPageSize() {
        return $this->pageSize;
    }

    /**
    * @return null before the first call to `getNextPage()`, or the total number of found items available for iteration
    */
    public function getTotal() {
        return $this->totalItems;
    }

    /**
    * Accessing found items is generally done through this method in a paginated fashion.
    * @return array the next set of items in the search with a maximum count set by `getPageSize()`
    */
    public function getNextPage() {
        if ($this->totalItems === null || $this->currentOffset === null ||
         $this->totalItems > $this->currentOffset) {
            if ($this->currentOffset === null) {
                $this->currentOffset = 0;
            } else {
                $this->currentOffset += $this->getPageSize();
            }

            $items = $this->sendRequest($this->currentOffset);
            $itemCount = sizeof($items);

            return Option::fromValue($items);
        } else {
            return None::create();
        }
    }

    /**
    * Allows to walk back through pages of results.
    * @return array items that were returned by `getNextPage()` before the last call to it
    */
    public function getPrevPage() {
        if ($this->currentOffset !== null && $this->currentOffset > 0) {
            $offset = $this->currentOffset - $this->getPageSize();
            if ($offset < 0) {
                $offset = 0;
            }

            $items = $this->sendRequest($offset);
            $itemCount = sizeof($items);

            $this->currentOffset = $offset;

            return Option::fromValue($items);
        } else {
            return None::create();
        }
    }
}
