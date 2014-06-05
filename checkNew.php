<?php

require_once 'Debug.php';
require_once 'Downloader.php';
require_once 'Feed.php';

Debug::init($argc > 1 ? $argv[1] : '');

$dl = new Downloader();
$feed = new Feed();

foreach ($feed->checkNewTorrents() as $link) {
	Debug::info("Adding link $link");
	$dl->addTorrent($link);
}
