<?php

namespace es\queries;

class MultiMatchQuery extends Query {
	private $_query;
	private $_fields = [];

	public function setFields(array $fields) {
		$this->_fields = $fields;
		return $this;
	}

	public function setQuery($query) {
		$this->_query = $query;
		return $this;
	}

	public function name() {
		return "multi_match";
	}

	public function asArray() {
		if (is_empty($this->_fields)) {
			throw new QueryException("you should set fields for MatchQuery");
		}

		$array = [];
		$array["fields"] = $this->_fields;
		if (!is_empty($this->_query)) {
			$array["query"] = $this->_query;
		}

		return $array;
	}
}

?>