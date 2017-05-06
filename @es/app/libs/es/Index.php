<?php

namespace es;

use es\api\Api;

class Index {
	private static $_indices = [];

	private $_id;
	private $_host = "127.0.0.1";
	private $_port = 9200;
	private $_name = "default";

	public function __construct($id) {
		$this->_id = $id;
	}

	public function host() {
		return $this->_host;
	}

	public function setHost($host) {
		$this->_host = $host;
	}

	public function port() {
		return $this->_port;
	}

	public function setPort($port) {
		$this->_port = $port;
	}

	public function name() {
		return $this->_name;
	}

	/**
	 * 获取API
	 *
	 * @param API类名
	 * @return Api
	 */
	public function api($class) {
		$prefix = "http://" . $this->_host . ":" . $this->_port;

		/**
		 * @var Api $obj
		 */
		$obj = new $class;
		$obj->prefix($prefix);

		return $obj;
	}

	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * @param $indexId
	 * @return self
	 * @throws Exception
	 */
	public static function indexWithId($indexId) {
		if (isset(self::$_indices[$indexId])) {
			return self::$_indices[$indexId];
		}

		$config = o("es.indices.{$indexId}");

		if (empty($config)) {
			throw new Exception("config for index '{$indexId}' is empty");
		}

		$index = new self($indexId);
		$index->_host = isset($config["host"]) ? $config["host"] : "127.0.0.1";
		$index->_port = isset($config["port"]) ? $config["port"] : 9200;
		$index->_name = isset($config["name"]) ? $config["name"] : "default";

		self::$_indices[$indexId] = $index;

		return $index;
	}
}

?>