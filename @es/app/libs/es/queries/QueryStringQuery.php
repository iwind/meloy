<?php

namespace es\queries;

/**
 * 查询语法参考文档：http://lucene.apache.org/core/6_4_1/queryparser/org/apache/lucene/queryparser/classic/package-summary.html
 *
 * @package es\queries
 */
class QueryStringQuery extends Query {
	private $_query;
	private $_defaultField;
	private $_defaultOperator;
	private $_fields = [];

	public function name() {
		return "query_string";
	}

	public function setQuery($query) {
		$this->_query = $query;
		return $this;
	}

	public function setFields(array $fields) {
		$this->_fields = $fields;
		return $this;
	}

	public function setDefaultField($field) {
		$this->_defaultField = $field;
		return $this;
	}

	public function setDefaultOperator($operator) {
		$this->_defaultOperator = $operator;
	}

	public function asArray() {
		$array = [];
		if (!pp_is_empty($this->_query)) {
			$array["query"] = $this->_query;
		}
		if (!pp_is_empty($this->_defaultField)) {
			$array["default_field"] = $this->_defaultField;
		}
		if (!pp_is_empty($this->_defaultOperator)) {
			$array["default_operator"] = $this->_defaultOperator;
		}
		if (!pp_is_empty($this->_fields)) {
			$array["fields"] = $this->_fields;
		}
		return $array;
	}
}

?>