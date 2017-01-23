<?php

namespace Alc\Addic7edCli\Component;

class SubtitleSelector
{
    /**
     * Suggest the best subtitle for the file
     *
     * @param string title
     * @param array subtitles
     *
     * @return int id
     */
    public function suggest($title, $subtitles)
    {
        if (count($subtitles) == 1) {
            return 1;
        }

        $hdTags = array('HD', '720p', '1080p');

        $hd = false;

        foreach ($hdTags as $hdTag) {
            if (stripos($title, $hdTag)) {
                $hd = true;
            }
        }

        $weights = array();

        foreach ($subtitles as $subtitle) {
            $weight = $subtitle->hd === true ? 1 : 0;

            $matches = $this->versionMatch($title, $subtitle->version);

            if ($matches) {
                $weight += $matches * 2;
            }

            $weights[$subtitle->id] = $weight;
        }

        arsort($weights);

        return key($weights);
    }

    /**
     * Version match
     *
     * @param string title
     * @param string version
     *
     * @return int matches
     */
    protected function versionMatch($title, $version)
    {
        $title = strtolower($title);
        $version = strtolower($version);

        $version = str_replace(array('.', '-', '/'), ' ', $version);
        $versionParts = explode(' ', $version);

        $matches = array();

        foreach ($versionParts as $versionPart) {
            if (strlen($versionPart) >= 3 && stripos($title, $versionPart)) {
                $matches[] = $versionPart;
            }
        }

        $matches = count($matches);

        if ($matches > 0) {
            return $matches;
        } else {
            return false;
        }
    }
}
