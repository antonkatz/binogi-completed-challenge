<?php

namespace App\Http\Controllers;

use App\RemoteServices\SpotifyInfo;
use App\RemoteServices\SpotifyTokenContainer;
use Illuminate\Http\Request;

/**
* Generates pages about Spotify object (tracks, albums, artists) with detailed information.
*/
class InfoController extends Controller
{
    /** Makes sure that the singleton info prodiver has the Spotify access token. */
    public function __construct(SpotifyTokenContainer $tc) {
        SpotifyInfo::setUp($tc);
    }

    /**
    * Constructs the information page irrespective of the object type.
    * @param href base64 encoded url to retrieve info about a Spotify object
    */
    public function info(string $href)
    {
        $info = SpotifyInfo::getInfo(\base64_decode($href));
        // there will be a list mapping Spotify type to a view function
        return view('info', (array) $info);
    }
}
