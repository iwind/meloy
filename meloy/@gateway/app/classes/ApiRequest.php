<?php

namespace gateway\app\classes;

class ApiRequest {
	private $_userAgent = "Meloy UI API";
	private $_timeout = 10;
	private $_prefix;

	/**
	 * 设置超时时间
	 *
	 * @param $timeout
	 */
	public function timeout($timeout) {
		$this->_timeout = $timeout;
	}

	/**
	 * 设置请求地址前缀
	 *
	 * @param $prefix
	 */
	public function prefix($prefix) {
		$this->_prefix = $prefix;
	}

	/**
	 * GET请求API
	 *
	 * @param $api
	 * @return ApiResponse
	 */
	public function get($api) {
		$curl = curl_init($this->_prefix . $api);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_HTTPGET, 1);

		//超时时间
		if (is_numeric($this->_timeout) && $this->_timeout > 0) {
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);
		}

		$response = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return $this->_parse($code, $response);
	}

	/**
	 * 执行请求
	 *
	 * @param $api
	 * @param $method
	 * @param string $headers
	 * @param string $body
	 * @return ApiResponse
	 */
	public function exec($api, $method, string $headers, string $body) {
		$curl = curl_init($this->_prefix . $api);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

		$headers = json_decode($headers, true);
		$formattedHeaders = [];
		if (is_array($headers)) {
			foreach ($headers as $header) {
				if (!is_array($header) || !isset($header["name"]) || !isset($header["value"])) {
					continue;
				}
				$formattedHeaders[] = $header["name"] . ": " . $header["value"];
			}
		}

		if ($method == "POST") {
			$formattedHeaders[] = "Content-Type: application/x-www-form-urlencoded";
		}

		if (!empty($formattedHeaders)) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $formattedHeaders);
		}

		// POST内容
		if (!is_empty($body)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		}

		// 超时时间
		if (is_numeric($this->_timeout) && $this->_timeout > 0) {
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);
		}

		$response = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return $this->_parse($code, $response);
	}

	private function _parse($code, $response) {
		$resp = new ApiResponse();
		if ($code != 200 && $code != 201) {
			$resp->setCode(0);
			$resp->setMessage($response);
			return $resp;
		}

		$json = json_decode($response);
		if (!is_object($json)) {
			$resp->setCode(0);
			$resp->setMessage($response);
			return $resp;
		}

		$resp->setCode($json->code ?? 0);
		$resp->setMessage($json->message ?? null);
		$resp->setData($json->data ?? null);

		return $resp;
	}
}

?>