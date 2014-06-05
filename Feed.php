<?php

require_once 'Config.php';

class Feed {

	private $logPath = null;
	private $rssUrl = null;

	public function __construct() {
		$config = Config::get();
		if (!isset($config[Config::LOG_PATH], $config[Config::RSS_URL])) {
			throw new Exception("Missing config items!");
		}
		$this->rssUrl = $config[Config::RSS_URL];
		$this->logPath = $config[Config::LOG_PATH];

		if (!is_writeable($this->logPath)) {
			throw new Exception("Location {$this->logPath} cannot be written to!");
		}
	}

	public function checkNewTorrents() {
		$xmlSource = @file_get_contents($this->rssUrl);
		$xml = simplexml_load_string($xmlSource);

		if (!$xml) {
			throw new Exception("Failed loading XML from {$this->rssUrl}");
		}

		Debug::info("Loaded source from {$this->rssUrl}");

		if (count($xml) == 0) {
			Debug::info('No new shit');
			return;
		}

		$lastCheckTime = @file_get_contents($this->logPath.'last');
		if (!$lastCheckTime) {
			Debug::info('empty last check file');
		}

		if (!isset($xml->channel->item)) {
			throw new Exception("Invalid XML format; expected channel->item elements");
		}

		$newTorrents = array();
		foreach ($xml->channel->item as $item) {
			if (strtotime($item->pubDate) >= $lastCheckTime) {
				$newTorrents[] = (string)$item->link;
				Debug::info("New item found: {$item->title}");
			} else {
				Debug::verbose("Old item found: {$item->title}");
			}
		}
		file_put_contents($this->logPath.'last', time());

		return $newTorrents;
	}
}
