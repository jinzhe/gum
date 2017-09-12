<?php
class net {
	public static function fetch($url) {
		set_time_limit(0);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		curl_close($curl);
		if (empty($result)) {
			$result = false;
		}
		return $result;
	}

	public static function ipAddress($ip) {
		$data = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
		$data = self::fetch($data);
		if (empty($data)) {
			return '中国';
		}

		$json = json_decode($data);
		//print_r($json);exit;
		if ($json->code != 0) {
			return 'LAN';
		} else {
			$result = array();
			if ($json->data->country != "中国") {
				$result[] = $json->data->country;
			}
			$result[] = $json->data->region;
			$result[] = $json->data->city;
			$result[] = $json->data->area;
			$result[] = " " . $json->data->isp;
			$result = implode("", $result);
			// print_r($result);exit;
			return $result;

		}
	}
}