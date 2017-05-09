<?php

namespace  es\scripts;

/**
 * Class Script
 *
 * API文档：https://www.elastic.co/guide/en/elasticsearch/reference/current/painless-api-reference.html
 *
 * @package es
 */
class Script {
	private $_name;
	private $_lang = "painless";
	private $_inline;
	private $_params = [];
	private $_file;

	public function __construct($name) {
		$this->_name = $name;
	}

	public function name() {
		return $this->_name;
	}

	public function setLang($lang) {
		$this->_lang = $lang;
		return $this;
	}

	public function setInline($inline) {
		$this->_inline = $inline;
		return $this;
	}

	public function setParams(array $params) {
		$this->_params = $params;
		return $this;
	}

	public function setFile($file) {
		$this->_file = $file;
		return $this;
	}

	public function asArray() {
		$arr = [];
		if (!is_null($this->_inline)) {
			$arr["inline"] = $this->_inline;
		}
		if (!is_null($this->_file)) {
			$arr["file"] = $this->_file;
		}
		if (!is_null($this->_lang)) {
			$arr["lang"] = $this->_lang;
		}
		if (!empty($this->_params)) {
			$arr["params"] = $this->_params;
		}
		return $arr;
	}
}

?>