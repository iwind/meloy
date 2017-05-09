<?php

namespace es\queries;

class RegexpQuery extends TermQuery {
	private $_regexp;
	private $_flags = [];

	public function name() {
		return "regexp";
	}

	public function setRegexp($regexp, array $flags = []) {
		$this->_regexp = $regexp;
		$this->_flags = $flags;
		return $this;
	}

	public function setFlags(array $flags) {
		$this->_flags = $flags;
		return $this;
	}

	public function asArray() {
		$query = [
			"value" => $this->_regexp
		];
		if (!empty($this->_flags)) {
			$query["flags"] = implode("|", $this->_flags);
		}
		return [
			$this->field() => $query
		];
	}
}

?>