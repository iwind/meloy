<?php

namespace es;

class Index {
	private static $_indices = [];

	private $_id;
	private $_host = "127.0.0.1";
	private $_port = 9200;
	private $_name = "default";

	private $_api;

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

	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * @return API
	 */
	public function api() {
		if (!$this->_api) {
			$this->_api = new API($this->_host, $this->_port);
		}
		return $this->_api;
	}

	public function exists() {
		return $this->api()->existIndex($this->_name);
	}

	public function create() {
		return $this->api()->createIndex($this->_name);
	}

	public function drop() {
		return $this->api()->dropIndex($this->_name);
	}

	public function putMapping(Mapping $mapping) {
		return $this->api()->putMapping($this->_name, $mapping->name(), $mapping->asArray());
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