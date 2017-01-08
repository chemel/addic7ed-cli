<?php

namespace Alc\Addic7edCli\Component;

class FilenameParser
{
    private $filename;
    private $title;
    private $season;
    private $episode;

    public function __construct($filename)
    {
        $this->parse($filename);
    }

    /**
     * Parse
     *
     * @param string filename
     */
    public function parse($filename)
    {
        $this->setFilename($filename);

        $pattern = '/(.+)S(\d{1,2})E(\d{1,2})/i';

        $success = preg_match($pattern, $filename, $matches);

        if (!$success) {
            return;
        }

        $this->setTitle(preg_replace('/\W+/', ' ', $matches[1]));
        $this->setSeason((int)$matches[2]);
        $this->setEpisode((int)$matches[3]);
    }

    /**
     * Get the value of Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }


    /**
     * Set the value of Filename
     *
     * @param string filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the value of Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param mixed title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Season
     *
     * @return string
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set the value of Season
     *
     * @param mixed season
     *
     * @return self
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get the value of Episode
     *
     * @return string
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * Set the value of Episode
     *
     * @param mixed episode
     *
     * @return self
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;

        return $this;
    }
}
