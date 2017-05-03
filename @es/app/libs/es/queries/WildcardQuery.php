<?php

namespace es\queries;

class WildcardQuery extends TermQuery {
	private $_wildcard;

	public function name() {
		return "wildcard";
	}

	public function setWildCard($wildcard) {
		$this->_wildcard = $wildcard;
		return $this;
	}

	public function asArray() {
		return [
			$this->field() => [
				"value" => $this->_wildcard
			]
		];
	}
}

?>