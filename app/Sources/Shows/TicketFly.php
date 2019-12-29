<?php

namespace App\Sources\Shows;

use App\Contracts\ShowSource;
use App\Models\Band;
use App\Models\Venue;
use Carbon\Carbon;

class TicketFly implements ShowSource
{
    /**
     * Imports shows for all venues
     */
    public function import()
    {
        foreach (Venue::getAllForDomain('ticketfly.com') as $venue) {
            $this->importFromVenue($venue);
        }
    }

    /**
     * Imports all the shows for a venue
     *
     * @param $venue
     */
    protected function importFromVenue($venue)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $venue->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $page = curl_exec($ch);
        curl_close($ch);

        $shows = $this->parsePage($page);
        foreach ($shows as $show) {
            $this->importShow($venue->id, $show);
        }
    }

    /**
     * Imports show and band data into the database
     *
     * @param $venueId
     * @param $show
     */
    protected function importShow($venueId, $show)
    {
        $newShow = [
            'venue_id' => $venueId,
            'show_date' => new Carbon($show->startDate),
        ];

        $bands = array_map('trim', explode(',', $show->name));
        foreach ($bands as $bandName) {
            $band = Band::findOrCreate($bandName);
            if (!$band->isDuplicateShow($venueId, $newShow['show_date'])) {
                $band->shows()->create($newShow);
            }
        }
    }

    /**
     * Parses the show data from an html page
     *
     * @param $page
     * @return mixed
     */
    protected function parsePage($page)
    {
        libxml_use_internal_errors(true); // Allow imperfect HTML

        $doc = new \DOMDocument();
        $doc->loadHTML($page, LIBXML_NOWARNING);
        $scripts = $doc->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if ($script->getAttribute('type') === 'application/ld+json') {
                return json_decode($script->nodeValue);
            }
        }

        return [];
    }
}
