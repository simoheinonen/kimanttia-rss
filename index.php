<?php

require_once 'vendor/autoload.php';

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;
use Symfony\Component\DomCrawler\Crawler;

$wwq = file_get_contents('https://radioplay.fi/podcast/kimanttia/');

$crawler = new Crawler($wwq);

$crawler = $crawler->filter('#play_history_list > ul > li > div.recently-played-track > a');


$feed = new Feed();

$channel = new Channel();
$channel
    ->title('Kimanttia')
    ->appendTo($feed);


foreach ($crawler as $domElement) {
    $title = $domElement->textContent;
    $url = 'https://radioplay.fi' . $domElement->getAttribute('href');

    $item = new Item();
    $item
        ->title($title)
        ->url($url)
        ->appendTo($channel);
}

header('Content-Type: application/xml; charset=utf-8');

echo $feed->render();
