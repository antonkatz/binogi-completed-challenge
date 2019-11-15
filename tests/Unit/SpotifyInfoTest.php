<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\RemoteServices\SpotifyInfo;
use App\RemoteServices\SpotifyTokenContainer;
use PHPOption\{Some, None};

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;


/**
* Tests for SpotifyInfo class which retrievs detailed info about any Spotify object.
*/
class SpotifyAuthTest extends TestCase
{
    private static $ARTIST_HREF = 'aHR0cHM6Ly9hcGkuc3BvdGlmeS5jb20vdjEvYXJ0aXN0cy82TWEzWDhiOVR0U1NLakZlaHlJNGV6';
    private static $ALBUM_HREF = 'aHR0cHM6Ly9hcGkuc3BvdGlmeS5jb20vdjEvYWxidW1zLzJCZXlNUTIwNlBreFFocE1Fd0pFeEI=';
    private static $TRACK_HREF = 'aHR0cHM6Ly9hcGkuc3BvdGlmeS5jb20vdjEvdHJhY2tzLzNTTjRiT3Y0VFFxZ0xlUjFzTXF0b2g=';

    private static $hasSetup = false;

    /**
    * Setting up the access token just once before any tests run.
    * This should be done with setUpBeforeClass(), however, it throws an error similar to this issue:
    * "Class config cannot be found" https://github.com/laravel/dusk/issues/99
    * My best guess is that Laravel is not ready at the point that setUpBeforeClass() executes.
    */
    public function setUp(): void {
        if (!self::$hasSetup) {
            parent::setUp();
            $tokenContainer = new SpotifyTokenContainer();
            SpotifyInfo::setUp($tokenContainer);

            self::$hasSetup = true;
        }
    }

    /**
    * An artist object should be formatted correctly.
    */
    public function test_retrieveArtistInfo() {
        $artistInfo = SpotifyInfo::getInfo(base64_decode(self::$ARTIST_HREF));
        $this->assertNotEmpty($artistInfo->name);
        $this->assertIsArray($artistInfo->genres);
        $this->assertIsInt($artistInfo->followersCount);
        $this->assertIsArray($artistInfo->images);
    }

    /**
    * An album object should be formatted correctly.
    */
    public function test_retrieveAlbumInfo() {
        $albumInfo = SpotifyInfo::getInfo(base64_decode(self::$ALBUM_HREF));
        $this->assertIsArray($albumInfo->genres);
        $this->assertNotEmpty($albumInfo->name);
        $this->assertNotEmpty($albumInfo->releaseDate);
        $this->assertIsInt($albumInfo->tracksCount);
        $this->assertIsArray($albumInfo->images);
    }

    /**
    * An track object should be formatted correctly.
    */
    public function test_retrieveTrackInfo() {
        $trackInfo = SpotifyInfo::getInfo(base64_decode(self::$TRACK_HREF));
        $this->assertNotEmpty($trackInfo->name);
        $this->assertNotEmpty($trackInfo->albumName);
        $this->assertIsInt($trackInfo->popularity);
        $this->assertIsArray($trackInfo->images);
    }

    /**
    * Given a list of URI's pointing to locations of different objects, SpotifyInfo should retrive well formatted info for all items in the list.
    */
    public function test_retrieveAllInfoFromList() {

    }
}
