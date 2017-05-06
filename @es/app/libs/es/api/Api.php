<?php

namespace es\api;

use es\Index;
use es\Query;
use tea\Exception;

class Api {
	private $_prefix;
	private $_userAgent = "PutData UI API";
	private $_cost;
	private $_index;
	private $_type;
	private $_params = [];
	private $_query;
	private $_payload;
	private $_code = 0;
	private $_data;

	protected $_endPoint;
	protected $_docs = [];

	private $_putCurls = [];

	public function __construct() {

	}

	public function userAgent($userAgent = nil) {
		if (!is_nil($userAgent)) {
			$this->_userAgent = $userAgent;
			return $this;
		}
		return $this;
	}

	public function endPoint($endPoint = nil) {
		if (!is_nil($endPoint)) {
			$this->_endPoint = $endPoint;
			return $this;
		}
		return $this->_endPoint;
	}

	public function docs(array $docs = NilArray) {
		if (!is_nil($docs)) {
			$this->_docs = $docs;
			return $this;
		}
		return $this->_docs;
	}

	public function cost() {
		return $this->_cost;
	}

	public function data() {
		return $this->_data;
	}

	public function dataAsJson($pretty = true) {
		if ($pretty) {
			return json_encode($this->_data, JSON_PRETTY_PRINT);
		}
		return json_encode($this->_data);
	}

	public function prefix($prefix = nil) {
		if (!is_nil($prefix)) {
			$this->_prefix = $prefix;
			return $this;
		}
		return $this->_prefix;
	}


	/**
	 * 设置要操作的索引
	 *
	 * @param string $index 要操作的索引
	 * @return string|static
	 */
	public function index($index = nil) {
		if (!is_nil($index)) {
			$this->_index = $index;
			return $this;
		}
		return $this->_index;
	}

	/**
	 * 设置要操作的类型
	 *
	 * @param string $type 要操作的类型
	 * @return string|static
	 */
	public function type($type = nil) {
		if (!is_nil($type)) {
			$this->_type = $type;
			return $this;
		}
		return $this->_type;
	}

	public function params(array $params = NilArray) {
		if (!is_nil($params)) {
			$this->_params = $params;
			return $this;
		}
		return $this->_params;
	}

	public function param($name, $value) {
		$this->_params[$name] = $value;
		return $this;
	}

	public function payload($payload = nil) {
		if (!is_nil($payload)) {
			$this->_payload = $payload;
			return $this;
		}
		return $this->_payload;
	}

	public function query($query = nil) {
		if (!is_nil($query)) {
			$this->_query = $query;

			if (is_string($query)) {
				$this->payload($query);
			}
			else if ($query instanceof Query) {
				$this->payload($query->asJson());
			}
			else if (is_array($query) || is_object($query)) {
				$this->payload(json_encode($query));
			}
		}
		return $this->_query;
	}

	/**
	 * 发送GET请求
	 */
	public function sendGet() {
		$curl = curl_init($this->_buildUrl());

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			"Connection: Keep-Alive",
			"Keep-Alive: 300",
			"Content-Type: application/json"
		));

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_HTTPGET, 1);

		if (strlen($this->_payload) > 0) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_payload);
		}

		$t = microtime(true);
		$response = curl_exec($curl);
		$this->_cost = microtime(true) - $t;

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		$this->_parse($code, $response);
	}

	public function sendPost() {
		$curl = curl_init($this->_buildUrl());
		curl_setopt($curl, CURLOPT_POST, 1);
		if (!is_empty($this->_payload)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_payload);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$this->_parse($code, $response);
	}

	public function sendHead() {
		$curl = curl_init($this->_buildUrl());
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_NOBODY, 1);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$this->_parse($code, $response);
	}

	public function sendPut() {
		$api = $this->_buildUrl();
		if (isset($this->_putCurls[$api])) {
			$curl = $this->_putCurls[$api];
		}
		else {
			$curl = curl_init($api);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);

			$this->_putCurls[$api] = $curl;
		}
		if (!is_empty($this->_payload)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_payload);
		}
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		//curl_close($curl);

		$this->_parse($code, $response);
	}

	public function sendDelete() {
		$curl = curl_init($this->_buildUrl());
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		if (!is_empty($this->_payload)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_payload);
		}

		$response = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$this->_parse($code, $response);
	}

	/**
	 * 根据索引对象构造查询
	 *
	 * @param Index $index 索引对象
	 * @return static
	 */
	public static function newWithIndex(Index $index) {
		$api = new static();
		$api->index($index->name());
		$api->prefix($index->apiPrefix());
		return $api;
	}

	private function _buildUrl() {
		$url = rtrim($this->_prefix, "/") . "/" . ltrim($this->_endPoint, "/");

		if (empty($this->_params)) {
			return $url;
		}
		if (preg_match("/\\?/", $this->_endPoint)) {
			$url .= "&" ;
		}
		else {
			$url .= "?";
		}

		return $url . http_build_query($this->_params);
	}

	private function _parse($code, $response) {
		$this->_code = $code;

		if (is_empty($response)) {
			throw new Exception("can not connect to server");
		}
		if ($code != 200) {
			if (substr($response, 0, 1) != "{") {
				throw new Exception("api response error:\n" . $response, $code);
			}
			else {
				if (is_cmd()) {
					throw new Exception("api response error:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT), $code);
				}
				else {
					throw new Exception("api response error:\n<pre>" . json_encode(json_decode($response), JSON_PRETTY_PRINT) . "</pre>", $code);
				}
			}
		}

		$this->_data = json_decode($response);
	}

	public function __toString() {
		return "";
	}
}

?>