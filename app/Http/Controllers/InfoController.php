<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfoController extends Controller
{
    /**
    * @param href base64 encoded url to retrieve info about a Spotify object
    */
    public function info(string $href)
    {
        // there will be a list mapping Spotify type to a view function
        return view('info');
    }
}
