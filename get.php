<?php

require_once 'Downloader.php';

if ($argc > 1) {
	$torrent = $argv[1];
	$dl = Downloader::getInstance('10.0.0.1:8081', 'aleksander.piekarz+torrent@gmail.com');
	if ($dl->add($torrent)) {
		echo "Torrent added!".PHP_EOL;
	} else {
		echo "Couldn't add torrent".PHP_EOL;
	}
}

