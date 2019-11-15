<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPOption\{Some, None, Option};

use App\RemoteServices\SpotifySearch;

/**
* Tests for Spotify search, that acts as a wrapper around the API and performs pagination.
*/
class SearchCommandsTest extends TestCase
{
    /**
    * Search results should be in pages that are iterable.
    */
    public function test_searchForward() {
        $results = [];

        $searchInstance = new SpotifySearch('a', 'artist');
        $this->assertGreaterThan(0, $searchInstance->getPageSize());

        $page = $searchInstance->getNextPage();
        $pageCount = 0;
        $expPageCount = 3;
        while($page instanceof Some && $pageCount < $expPageCount) {
            $results = array_merge($results, $page->get());
            $page = $searchInstance->getNextPage();
            $pageCount += 1;
            // also should assert that each page has new results
        }

        $this->assertEquals($expPageCount * $searchInstance->getPageSize(), sizeof($results));
        $this->assertGreaterThanOrEqual(0, sizeof($results));
        $this->assertGreaterThanOrEqual(sizeof($results), $searchInstance->getTotal());
    }

    /**
    * Search results should be navigatable backwards, and should be the same results that were given going forwards.
    */
    public function test_searchBackward() {
        $resultsForward = [];

        $searchInstance = new SpotifySearch('a', 'artist');
        $page = $searchInstance->getNextPage();
        $pageCount = 0;
        while($page instanceof Some && $pageCount < 3) {
            $resultsForward = array_merge($resultsForward, $page->get());
            $page = $searchInstance->getNextPage();
            $pageCount += 1;
        }

        $this->assertEquals($pageCount * $searchInstance->getPageSize(), sizeof($resultsForward));

        $resultsBackward = [];
        $page = $searchInstance->getPrevPage();
        while($page instanceof Some) {
            $resultsBackward = array_merge($resultsBackward, $page->get());
            $page = $searchInstance->getPrevPage();
        }

        $this->assertEquals(sizeof($resultsBackward), sizeof($resultsForward));

        $resultsForward = array_map('json_encode', $resultsForward);
        $resultsBackward = array_map('json_encode', $resultsBackward);
        $diff = array_diff($resultsBackward, $resultsForward);
        $this->assertEquals(0, sizeof($diff));
    }

    /**
    * Search results should be navigatable by page number.
    */
    public function test_searchByPage() {

    }

    /**
    * Search type should be of valid value.
    */
    public function test_searchTypeNotAllowed() {
        $this->expectException(\Exception::class);
        new SpotifySearch('any', 'not_allowed');
    }

    /**
    * Items returned by search should have certain fields present depending on the type of item.
    */
    public function test_resultsAreValidFormat() {
        $types = ['track'];

        foreach ($types as $tk => $type) {
            $searchInstance = new SpotifySearch('a', $type);
            $results = $searchInstance->getNextPage()->get();

            foreach ($results as $ik => $item) {
                $this->assertNotEmpty($item->href);
                $this->assertNotEmpty($item->name);
                if ($type != 'track') {
                    $this->assertNotEmpty($item->images);
                } else {
                    $this->assertNotEmpty($item->album);
                }
            }
        }

    }
}
