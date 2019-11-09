<?php

namespace App\RemoteServices;

use \PHPOption\Option;

class Spotify {
    public static function getSecretKey() {
        return Option::fromValue(config('services.spotify.secret'));
    }

    public static function getClientId() {
        return Option::fromValue(config('services.spotify.clientid'));
    }
}
