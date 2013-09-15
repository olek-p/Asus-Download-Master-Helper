<?php

require_once 'Downloader.php';
require_once 'Feed.php';

$dl = Downloader::getInstance('10.0.0.1:8081', 'aleksander.piekarz+torrent@gmail.com');

$feedUrl = 'http://showrss.karmorra.info/rss.php?user_id=136982&hd=1&proper=null&namespaces=true&magnets=true';

$feed = new Feed($feedUrl);

foreach ($feed->getNewTorrents() as $link) {
	echo date('Y-m-d H:i:s')."adding link $link".PHP_EOL;
	$dl->add($link);
}
