<?php

namespace App\RemoteServices;

use App\RemoteServices\WithClientTrait;

/**
* A sigleton used to retrive detailed info about any Spotify object.
* The setUp method must be ran once before the SpotifyInfo can be used.
*/
class SpotifyInfo {
    use WithClientTrait;

    /**
    * Sets up the HTTP client with correct authentication details.
    * @param SpotifyTokenContainer which contains an application-wide Spotify access token
    */
    public static function setUp(SpotifyTokenContainer $tokenContainer) {
        $headers = SpotifyAuth::generateAccessTokenHeaders($tokenContainer);
        self::setClientDefaults($headers);
    }

    /**
    * Given a url pointing to a Spotify object retrieves detailed info.
    * @return object well formatted object that contains different types of information based on its type.
    *                all objects have a `type` field
    */
    public static function getInfo($url) {
        $client = self::getClient();
        $response = $client->get($url);
        $object = json_decode($response->getBody());
        return self::formatObject($object);
    }

    private static function formatObject($obj) {
        $copy = clone $obj;
        switch($obj->type) {
            case 'artist':
                return self::formatArtistObject($copy);
            case 'album':
                return self::formatAlbumObject($copy);
            case 'track':
                return self::formatTrackObject($copy);
            default:
                $copy->type = 'unknown';
                return $copy;
        }
    }

    private static function formatArtistObject($artist) {
        $artist->followersCount = $artist->followers->total;
        return $artist;
    }

    private static function formatAlbumObject($album) {
        $album->releaseDate = $album->release_date;
        $album->tracksCount = sizeof($album->tracks->items);
        return $album;
    }

    private static function formatTrackObject($track) {
        $track->albumName = $track->album->name;
        $track->images = $track->album->images;
        return $track;
    }
}
