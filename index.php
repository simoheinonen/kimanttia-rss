<?php

require_once 'vendor/autoload.php';

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;
use Symfony\Component\DomCrawler\Crawler;

$wwq = file_get_contents('https://radioplay.fi/podcast/kimanttia/');

$crawler = new Crawler($wwq);

$crawler = $crawler->filter('#play_history_list > ul > li > div.recently-played-track');

$feed = new Feed();

$channel = new Channel();
$channel
    ->title('Kimanttia')
    ->appendTo($feed);


foreach ($crawler as $domElement) {
    $link = $domElement->getElementsByTagName('a')[0];

    $title = $link->textContent;
    $url = 'https://radioplay.fi' . $link->getAttribute('href');

    $stuff = $domElement->getElementsByTagName('span')[0];
    [$lengthDate, $description] = explode("\n", $stuff->textContent);
    [$length, $date] = explode(' • ', $lengthDate);

    $description = trim($description);

    $item = new Item();
    $item
        ->title($title)
        ->url($url)
        ->description($description)
        ->pubDate((new \DateTime($date))->getTimestamp())
        ->appendTo($channel);
}

header('Content-Type: application/xml; charset=utf-8');

echo $feed->render();
