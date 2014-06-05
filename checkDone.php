<?php

require_once 'Debug.php';
require_once 'Downloader.php';

Debug::init($argc > 1);

$dl = new Downloader();
$dl->checkActiveList();

