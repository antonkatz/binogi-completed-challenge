<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\RemoteServices\SpotifySearch;
use App\RemoteServices\SpotifyTokenContainer;

/**
* Displaying the search page and search results.
*/
class SearchController extends Controller
{
    private static $MISSING_IMAGE_URL = 'https://img.icons8.com/ios/50/000000/no-camera.png';

    /**
    * Found items are categorized by type. Each type has its own UI list,
    * and the order of lists is dictated by this constant
    */
    private static $SEARCH_TYPES = ['artist', 'album', 'track']; // these could live in a config file
    private static $TYPE_TO_DISPLAY_NAME = ['artist' => 'Artists',
                                'album' => 'Albums',
                                'track' => 'Tracks'];

    private $tokenContainer;

    /**
    * @param tokenContainer singleton containing a single application-wide access token for Spotify
    */
    public function __construct(SpotifyTokenContainer $tokenContainer) {
        $this->tokenContainer = $tokenContainer;
    }

    /**
    * Displays the front page with the search box.
    */
    public function index()
    {
        return view('index');
    }

    /**
    * Displays the search results page.
    */
    public function search(Request $request)
    {
        // all inner functions assume that the passed in item is cloned

        /** Used to simplify view construction by selecting a single image for  all items */
        function addSelectImage($item, $missingUrl) {
            if (isset($item->images) && !empty($item->images)) {
                $item->imageUrl = $item->images[0]->url;
            } else if (isset($item->album)) {
                $item->imageUrl = $item->album->images[0]->url;
            } else {
                $item->imageUrl = $missingUrl;
            }
            return $item;
        }

        function generateInfoLink($item) {
            $href = \base64_encode($item->href);
            // using action is the appropriate way to generate the url, however it does not seem
            // to work within Gitpod
            // $item->infoLink = action('InfoController@info', ['href' => $href]);
            $item->infoLink = '/info/' . $href;
            return $item;
        }

        $query = $request->get('query');
        // pagination would be achieved by keeping track of the page number through request url query parameters

        // NOTE: At this point, I realized that the `getNextPage()` and `getPreviousPage()` as means
        // of pagination are not applicable since the search class instances are discarded
        // at the end of each request.
        // I've been doing too much frontend work it seems :)

        $foundItems = [];
        foreach (self::$SEARCH_TYPES as $type) {
            $searchInstance = new SpotifySearch($query, $type, $this->tokenContainer);
            $res = $searchInstance->getNextPage()->get();
            $foundItems[$type] = array_map(function ($item) {
                $upd = clone $item;
                $upd = addSelectImage($upd, self::$MISSING_IMAGE_URL);
                return generateInfoLink($upd);
            }, $res);
        }

        return view('search', [
            'searchTerm' => $query,
            'items' => $foundItems,
            'typeToDisplayName' => self::$TYPE_TO_DISPLAY_NAME
        ]);
    }
}
