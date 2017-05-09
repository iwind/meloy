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
	private $_name;
	private $_params = [];
	private $_docUrl;
	private $_description;

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

	public function setName($name) {
		$this->_name = $name;
		return $this;
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

	public function setDescription($description) {
		$this->_description = $description;
		return $this;
	}

	public function description() {
		return $this->_description;
	}

	public function setDocUrl($docUrl) {
		$this->_docUrl = $docUrl;
		return $this;
	}

	public function docUrl() {
		return $this->_docUrl;
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

	public function supportsVersion($version) {
		return true;
	}

	/**
	 * 根据字段类型构造字段对象
	 *
	 * @param string $fieldType 字段类型代号
	 * @return Field
	 */
	public static function fieldWithType($fieldType) {
		$classPrefix = ucfirst(preg_replace_callback("/_(\\w)/", function ($match) {
			return strtoupper($match[1]);
		}, $fieldType));
		$className = __NAMESPACE__ . "\\" .  $classPrefix . "Field";

		/**
		 * 字段对象
		 *
		 * @var Field $field
		 */
		return new $className("");
	}

	public abstract function type();
}

?>