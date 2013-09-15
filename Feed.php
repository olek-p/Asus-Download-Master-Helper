<?php

class Feed {

	const LAST_CHECK_FILE = 'check/last';

	private $newTorrents = array();

	public function __construct($file_or_url) {
		if(!eregi('^http:', $file_or_url)) {
			$feed_uri = $_SERVER['DOCUMENT_ROOT'] .'/shared/xml/'. $file_or_url;
		} else {
			$feed_uri = $file_or_url;
		}

		$xml_source = file_get_contents($feed_uri);
		$x = simplexml_load_string($xml_source);

		if (count($x) == 0) {
			return;
		}

		$lastCheck = file_get_contents(dirname(__FILE__).'/'.self::LAST_CHECK_FILE);
		if (!$lastCheck) {
			return;
		}

		foreach($x->channel->item as $item) {
			if (strtotime($item->pubDate) >= $lastCheck) {
				$this->newTorrents[] = (string)$item->link;
			}
		}
		file_put_contents(dirname(__FILE__).'/'.self::LAST_CHECK_FILE, time());
	}

	public function getNewTorrents() {
		return $this->newTorrents;
	}
}
