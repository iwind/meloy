<?php

namespace es\queries;

class PrefixQuery extends TermQuery {
	private $_prefix;

	public function name() {
		return "prefix";
	}

	public function setPrefix($prefix) {
		$this->_prefix = $prefix;
		return $this;
	}

	public function asArray() {
		return [
			$this->field() => [
				"value" => $this->_prefix
			]
		];
	}
}

?>