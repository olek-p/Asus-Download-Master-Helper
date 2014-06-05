<?php

class Debug {
	const LEVEL_VERBOSE = 0;
	const LEVEL_INFO = 1;

	private static $debugLevel = 1000;

	public static function init($param) {
		switch ($param) {
			case '-v':
				self::$debugLevel = self::LEVEL_VERBOSE;
				break;
			case '-d':
				self::$debugLevel = self::LEVEL_INFO;
				break;
		}
	}

	public static function info($log) {
		self::log($log, self::LEVEL_INFO);
	}

	public static function verbose($log) {
		self::log($log, self::LEVEL_VERBOSE);
	}

	private static function log($log, $level) {
		if ($level >= self::$debugLevel) {
			echo sprintf('[%s] %s'.PHP_EOL, date('Y-m-d H:i:s'), $log);
		}
	}
}
