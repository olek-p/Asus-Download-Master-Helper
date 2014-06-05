<?php

require_once 'Debug.php';
require_once 'Downloader.php';

if ($argc > 1) {
	$torrent = $argv[1];
	$dl = new Downloader();
	if ($dl->addTorrent($torrent)) {
		echo "Torrent added!".PHP_EOL;
	} else {
		echo "Couldn't add torrent".PHP_EOL;
	}
}

