<?php

require_once 'Config.php';

class Downloader {

	private $dmIp = null;
	private $email = null;
	private $authKey = null;

	public function __construct() {
		$config = Config::get();
		if (!isset($config[Config::IP], $config[Config::EMAIL], $config[Config::AUTH_KEY])) {
			throw new Exception("Missing config items!");
		}
		$this->dmIp = $config[Config::IP];
		$this->email = $config[Config::EMAIL];
		$this->authKey = $config[Config::AUTH_KEY];
	}

	public function addTorrent($torrent) {
		$command = 'dm_apply.cgi?'.
			http_build_query(array(
				'action_mode' => 'DM_ADD',
				'download_type' => 5,
				'again' => 'no',
				'usb_dm_url' => $torrent,
				't' => $this->getRandomSignature(),
			));
		$result = $this->execCommand($command);

		if ($result == 'ACK_SUCESS') { // sic!
			return true;
		} else if ($result == 'BT_EXISTS') {
			return false;
		}
		throw new Exception("Unknown add result: $result");
	}

	public function checkActiveList() {
		$command = 'dm_print_status.cgi?'.
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
		if ($itemCount) {
			for ($i = 0; $i < $itemCount; $i++) {
				if ($matches['status'][$i] == 'Finished' || $matches['status'][$i] == 'Seeding') {
					$subject = 'Job "'.$matches['title'][$i].'" has finished';
					$message = $subject . '. Removing... ';
					if ($this->deleteJob($matches['id'][$i], $matches['type'][$i])) {
						$message .= 'OK!';
					} else {
						$message .= 'Not removed';
					}
					if ($this->email) {
						mail($this->email, $subject, $message);
					}
					Debug::info($message);
				}
			}
		} else {
			Debug::info('No active jobs');
		}
	}

	private function deleteJob($jobId, $type) {
		$command = 'dm_apply.cgi?'.
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
		$command = "http://{$this->dmIp}:8081/downloadmaster/$command";
		Debug::verbose("Calling command $command");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $command);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Basic {$this->authKey}"));

		$result = curl_exec($ch);
		$error = curl_error($ch);
		Debug::verbose("Got response... ".($result ? '' : '(empty) '.($error ? 'ERROR: '.$error : '')));

		curl_close($ch);

		return $result;
	}
}
