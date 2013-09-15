<?php

class Downloader {

	private static $instances = array();
	private $dmIp = NULL;
	private $email = NULL;

	private function __construct($ip, $email) {
		$this->dmIp = $ip;
		$this->email = $email;
	}

	public static function getInstance($ip, $email) {
		if (!isset(static::$instances[$ip])) {
			static::$instances[$ip] = new static($ip, $email);
		}
		return static::$instances[$ip];
	}

	public function add($torrent) {
		$command = 'http://'.$this->dmIp.'/downloadmaster/dm_apply.cgi?'.
			http_build_query(array(
				'action_mode' => 'DM_ADD',
				'download_type' => 5,
				'again' => 'no',
				'usb_dm_url' => $torrent,
				't' => $this->getRandomSignature(),
			));
		$result = $this->execCommand($command);

		if ($result == 'ACK_SUCESS') {
			return TRUE;
		} else if ($result == 'BT_EXISTS') {
			return FALSE;
		}
		throw new Exception("Unknown add result: $result");
	}

	public function getList() {
		$command = 'http://'.$this->dmIp.'/downloadmaster/dm_print_status.cgi?'.
			http_build_query(array(
				'action_mode' => 'All',
				't' => $this->getRandomSignature(),
			));
		$result = $this->execCommand($command);
		$result = (str_replace(PHP_EOL,'',$result));
		preg_match_all('@'
			.'"(?<id>[^"]*)",'
			.'"(?<title>[^"]*)",'
			.'"(?<dlratio>[^"]*)",'
			.'"(?<size>[^"]*)",'
			.'"(?<status>[^"]*)",'
			.'"(?<type>[^"]*)",'
			.'"(?<time>[^"]*)",'
			.'"(?<dlspeed>[^"]*)",'
			.'"(?<ulspeed>[^"]*)",'
			.'"(?<peers>[^"]*)",'
			.'"(?<nothing>[^"]*)",'
			.'"(?<someratio>[^"]*)",'
			.'"(?<path>[^"]*)"'
			.'@', $result, $matches);
		$itemCount = count($matches['id']);
		for ($i = 0; $i < $itemCount; $i++) {
			if ($matches['status'][$i] == 'Finished' || $matches['status'][$i] == 'Seeding') {
				$subject = 'Job "'.$matches['title'][$i].'" has finished';
				$message = $subject . '. Removing... ';
				if ($this->deleteJob($matches['id'][$i], $matches['type'][$i])) {
					$message .= 'OK!';
				} else {
					$message .= 'Not removed';
				}
				mail($this->email, $subject, $message);
			}
		}
	}

	private function deleteJob($jobId, $type) {
		$command = 'http://'.$this->dmIp.'/downloadmaster/dm_apply.cgi?'.
			http_build_query(array(
				'action_mode' => 'DM_CTRL',
				'dm_ctrl' => 'cancel',
				'task_id' => $jobId,
				'download_type' => $type,
				't' => $this->getRandomSignature(),
			));
		$result = $this->execCommand($command);

		return $result == 'ACK_SUCESS';
	}

	private function getRandomSignature() {
		$result = '0.';
		for ($i = 0; $i < 16; $i++) {
			$result .= rand(0, 9);
		}
		return $result;
	}

	private function execCommand($command) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $command);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic YWRtaW46QWRtaW4='));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
