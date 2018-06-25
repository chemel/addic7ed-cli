<?php

namespace Alc\Addic7edCli\Scrapper;

use Symfony\Component\DomCrawler\Crawler;

class Addic7edScrapper
{
    protected $client;

    protected $baseUrl = 'http://www.addic7ed.com';

    /**
     * Constructor
     *
     * @param Client client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get HTTP client
     *
     * @return Client client
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * Get Parser
     *
     * @param string $html
     *
     * @return Crawler crawler
     */
    protected function getParser($html)
    {
        return new Crawler($html);
    }

    /**
     * HTTP GET request
     *
     * @param string url
     *
     * @return Response response
     */
    protected function get($url)
    {
        $client = $this->getClient();

        // echo '[GET] ', $url, "\n";

        return $client->request('GET', $url);
    }

    /**
     * Parse HTML page
     *
     * @param string url
     *
     * @return Crawler parser
     */
    protected function parse($url)
    {
        $html = $this->get($url)->getBody()->getContents();

        $parser = $this->getParser($html);

        return $parser;
    }

    /**
     * Search
     *
     * @param string term
     *
     * @return stdClass data
     */
    public function search($term)
    {
        $url = $this->baseUrl.'/search.php?search='.urlencode($term).'&Submit=Search';

        $parser = $this->parse($url);

        $data = new \stdClass();

        $parser->filter('span.titulo a')->each(function (Crawler $node) use ($data) {
            $data->showUrl = $this->baseUrl.'/'.$node->attr('href');
            $data->showId = substr($node->attr('href'), strrpos($node->attr('href'), '/')+1);
        });

        $data->results = array();

        // Parse all results page
        $parser->filter('table.tabel a')->each(function (Crawler $node) use ($data) {
            $result = new \stdClass();
            $result->title = $node->text();
            $result->url = $this->baseUrl.'/'.$node->attr('href');
            $data->results[] = $result;
        });

        // If show id not found, find an otherway to get it!
        if(!isset($data->showUrl) && count($data->results) > 0) {
            // Query as results, now find the show id
            $result = $data->results[0];

            $parser = $this->parse($result->url);

            $showIds = $parser->filter('table.tabel70 a')->each(function (Crawler $node) use ($data) {
                $href = $node->attr('href');
                if(substr($href, 0, 6) == '/show/') {
                    $showId = substr($href, 6);
                    if(is_numeric($showId)) {
                        return $showId;
                    }
                }
            });

            // Remove NULL values
            $showIds = array_filter($showIds);

            // Take the first id
            foreach($showIds as $showId) {
                if(is_numeric($showId)) {
                    $data->showId = $showId;
                    $data->showUrl = $this->baseUrl.'/show/'.$showId;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Show
     *
     * @param int showId
     * @param int season
     *
     * @return array data
     */
    public function show($showId, $season = 1)
    {
        $url = $this->baseUrl.'/ajax_loadShow.php?'.http_build_query(array(
            'show' => $showId,
            'season' => $season,
            'langs' => '',
            'hd' => 'undefined',
            'hi' => 'undefined',
        ));

        $parser = $this->parse($url);

        $data = array();

        $parser->filter('div#season table tr.epeven')->each(function (Crawler $node) use (&$data) {
            $result = new \stdClass();

            $node->filter('td')->each(function (Crawler $node, $i) use ($result) {
                switch ($i) {
                    case 0:
                        $result->season = $node->text();
                        break;
                    case 1:
                        $result->episode = $node->text();
                        break;
                    case 2:
                        $result->title = $node->text();
                        break;
                    case 3:
                        $result->language = $node->text();
                        break;
                    case 4:
                        $result->version = $node->text();
                        break;
                    case 5:
                        $result->completed = $node->text();
                        break;
                    case 6:
                        $result->hearingImpaired = !empty($node->text());
                        break;
                    case 7:
                        $result->corrected = !empty($node->text());
                        break;
                    case 8:
                        $result->hd = !empty($node->text());
                        break;
                    case 9:
                        $result->url = $this->baseUrl.$node->filter('a')->first()->attr('href');
                        break;
                }
            });

            $data[] = $result;
        });

        return $data;
    }
}
