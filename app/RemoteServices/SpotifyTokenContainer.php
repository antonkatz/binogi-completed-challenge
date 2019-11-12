<?php

namespace App\RemoteServices;

use App\RemoteServices\TokenContainer;
use App\RemoteServices\SpotifyAuth;
use Illuminate\Support\Facades\Log;

class SpotifyTokenContainer extends TokenContainer {
    protected function retrieveToken() {
        return SpotifyAuth::retrieveAccessToken();
    }

    protected function sendMailWarning($message) {
        Log::info('Issue retrieving Spotify access token: ' . $message);
    }
}
