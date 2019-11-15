<?php

namespace Tests\Unit\Helpers;

use App\RemoteServices\SpotifyTokenContainer;

class SingletonTokenContainer {
    private static $TC;

    public static function get() {
        if (!self::$TC) {
            self::$TC = new SpotifyTokenContainer();
        }
        return self::$TC;
    }
}
