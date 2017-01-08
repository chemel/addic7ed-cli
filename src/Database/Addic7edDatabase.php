<?php

namespace Alc\Addic7edCli\Database;

use Alc\Addic7edCli\Scrapper\Addic7edScrapper;

class Addic7edDatabase
{
    private $scrapper;

    private $searches = array();
    private $shows = array();

    /**
     * Constructor
     *
     * @param Client client
     */
    public function __construct($client)
    {
        $this->scrapper = new Addic7edScrapper($client);
    }

    /**
     * Get show id
     *
     * @param string term
     *
     * @return int showId
     */
    public function getShowId($term)
    {
        $lowerTerm = strtolower($term);

        if (isset($this->searches[$lowerTerm])) {
            return $this->searches[$lowerTerm];
        }

        $searchData = $this->scrapper->search($term);

        if(!isset($searchData->showId)) return;

        $showId = $searchData->showId;

        return $this->searches[$lowerTerm] = $showId;
    }

    /**
     * Get show data
     *
     * @param int showId
     *
     * @return array showData
     */
    public function getShowData($showId, $season=1)
    {
        if (isset($this->shows[$showId][$season])) {
            return $this->shows[$showId][$season];
        }

        $showData = $this->scrapper->show($showId, $season);

        return $this->shows[$showId][$season] = $showData;
    }

    /**
     * Find
     *
     * @param string searchTerm
     * @param string language
     * @param int season
     * @param int episode
     *
     * @return array results
     */
    public function find($searchTerm, $language, $season=1, $episode=null)
    {
        $showId = $this->getShowId($searchTerm);

        if(!$showId) return array();

        $showData = $this->getShowData($showId, $season);

        $results = array();

        foreach ($showData as $show) {
            if ($show->language != $language) {
                continue;
            }
            if ($show->episode != $episode) {
                continue;
            }

            $results[] = $show;
        }

        return $results;
    }
}
