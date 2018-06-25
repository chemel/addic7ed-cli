<?php

require __DIR__.'/../vendor/autoload.php';

use Alc\Addic7edCli\Component\HttpClient;
use Alc\Addic7edCli\Scrapper\Addic7edScrapper;

$client = new HttpClient();
$client = $client->getClient();
$scraper = new Addic7edScrapper($client);

$data = $scraper->search('Game of Thrones');

print_r($data);

// $data = $scraper->show(1245);
//
// print_r($data);
