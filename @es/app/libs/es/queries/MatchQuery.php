<?php

namespace es\queries;

class MatchQuery extends Query {
	private $_query;
	private $_field;

	public function setField($field) {
		$this->_field = $field;
		return $this;
	}

	public function setQuery($query) {
		$this->_query = $query;
		return $this;
	}

	public function name() {
		return "match";
	}

	public function asArray() {
		if (is_empty($this->_field)) {
			throw new QueryException("you should set field for MatchQuery");
		}

		$array = [];
		if (!is_empty($this->_query)) {
			$array["query"] = $this->_query;
		}

		return [
			$this->_field => $array
		];
	}
}

?>