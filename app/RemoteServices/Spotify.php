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

    public static function generateAuthHeader() {
        $secret = Spotify::getSecretKey()->get();
        $id = Spotify::getClientId() -> get();
        $b64 = base64_encode($id . ":" . $secret);
        return 'Basic ' . $b64;
    }
}
