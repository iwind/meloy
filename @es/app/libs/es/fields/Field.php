<?php

namespace es\fields;

/**
 * Class Field
 *
 * 参考:https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html
 *
 * @package es\fields
 */
abstract class Field {
	const TYPE_STRING = "string";
	const TYPE_LONG = "long";
	const TYPE_INTEGER = "integer";
	const TYPE_SHORT = "short";
	const TYPE_BYTE = "byte";
	const TYPE_DOUBLE = "double";
	const TYPE_FLOAT = "float";
	const TYPE_DATE = "date";
	const TYPE_BOOLEAN = "boolean";
	const TYPE_BINARY = "binary";

	const TYPE_OBJECT = "object";
	const TYPE_NESTED = "nested";

	const TYPE_GEO_POINT = "geo_point";
	const TYPE_GEO_SHAPE = "geo_shape";

	const TYPE_IP = "ip";
	const TYPE_COMPLETION = "completion";
	const TYPE_TOKEN_COUNT = "token_count";
	const TYPE_MURMUR3 = "murmur3";
	const TYPE_ATTACHMENT = "attachment";

	private $_name;
	private $_params = [];

	/**
	 * @var self[]
	 */
	private $_children = [];

	public function __construct($name, array $params = []) {
		$this->_name = $name;
		$this->_params = $params;
	}

	public function name() {
		return $this->_name;
	}

	public function setParam($name, $value) {
		$this->_params[$name] = $value;
		return $this;
	}

	public function param($name) {
		if (isset($this->_params[$name])) {
			return $this->_params[$name];
		}
		return null;
	}

	public function addChild(Field $childField) {
		$this->_children[] = $childField;
		return $this;
	}

	public function children() {
		return $this->_children;
	}

	public function asArray() {
		$params = $this->_params;
		$params["type"] = $this->type();

		$fields = [];
		if (!empty($this->_children)) {
			foreach ($this->_children as $child) {
				$fields[$child->name()] = $child->asArray();
			}
			$params["fields"] = $fields;
		}

		return $params;
	}

	public function asJson() {
		return json_encode($this->asArray());
	}

	public function asPrettyJson() {
		return json_encode($this->asArray(), JSON_PRETTY_PRINT);
	}

	public abstract function type();
}

?>