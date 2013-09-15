<?php

require_once 'Downloader.php';

$dl = Downloader::getInstance('10.0.0.1:8081', 'aleksander.piekarz+torrent@gmail.com');
$dl->getList();

