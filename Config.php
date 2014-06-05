<?php

class Config {
	const EMAIL = 'email';
	const IP = 'ip';
	const AUTH_KEY = 'auth';
	const LOG_PATH = 'log_path';
	const RSS_URL = 'rss';

	private static $config = array(
		self::EMAIL => '<your email here>',
		self::IP => '192.168.1.1',
		self::AUTH_KEY => '<your key here>',
		self::LOG_PATH => '/var/log/torrent/check/',
		self::RSS_URL => 'http://showrss.info/rss.php?user_id=<your user id here>&hd=1&proper=null&namespaces=true&magnets=true',
	);

	public static function get() {
		return self::$config;
	}
}
