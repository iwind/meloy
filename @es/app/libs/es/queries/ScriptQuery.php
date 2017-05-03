<?php

namespace es\queries;

class ScriptQuery extends Query {
	private $_inline;
	private $_lang = "painless";
	private $_params = [];

	public function name() {
		return "script";
	}

	public function setInline($inline) {
		$this->_inline = $inline;
		return $this;
	}

	public function setLang($lang) {
		$this->_lang = $lang;
		return $this;
	}

	public function setParams(array $params) {
		$this->_params = $params;
		return $this;
	}

	public function asArray() {
		$arr = [
			"inline" => $this->_inline,
			"lang" => $this->_lang,
		];
		if (!empty($this->_params)) {
			$arr["params"] = $this->_params;
		}
		return [
			"script" => $arr
		];
	}
}

?>