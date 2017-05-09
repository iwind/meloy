<?php


namespace tea\db;

/**
 * 字段类
 *
 * @package tea\db
 */
class Field {
	const TYPE_INT = 1;
	const TYPE_LONG = 2;
	const TYPE_BOOL = 3;
	const TYPE_LOB = 4;
	const TYPE_NULL = 5;
	const TYPE_STRING = 6;
	const TYPE_FLOAT = 7;
	const TYPE_DOUBLE = 8;
	const TYPE_DATE = 9;
	const TYPE_TIME = 10;
	const TYPE_TIMESTAMP = 11;

	private $_name;
	private $_type;
	private $_fullType;
	private $_dataType = 0;
	private $_length = 0;
	private $_defaultValue;
	private $_autoIncrement;
	private $_primaryKey;
	private $_notNull;
	private $_scale = 0;
	private $_comment;
	private $_collation;

	public function __construct() {

	}

	public function setName($name) {
		$this->_name = $name;
	}

	public function name() {
		return $this->_name;
	}

	public function setDataType($dataType) {
		$this->_dataType = $dataType;
	}

	public function dataType() {
		return $this->_dataType;
	}

	public function isDataType($dataType) {
		return ($this->_dataType == $dataType);
	}

	public function setType($type) {
		$this->_type = $type;
	}

	public function type() {
		return $this->_type;
	}

	public function setFullType($fullType) {
		$this->_fullType = $fullType;
	}

	public function fullType() {
		return $this->_fullType;
	}

	public function setLength($length) {
		$this->_length = $length;
	}

	public function length() {
		return $this->_length;
	}

	public function setDefaultValue($defaultValue) {
		$this->_defaultValue = $defaultValue;
	}

	public function defaultValue() {
		return $this->_defaultValue;
	}

	public function setAutoIncrement($autoIncrement) {
		$this->_autoIncrement = $autoIncrement;
	}

	public function autoIncrement() {
		return $this->_autoIncrement;
	}

	public function setPrimaryKey($primaryKey) {
		$this->_primaryKey = $primaryKey;
	}

	public function isPrimaryKey() {
		return $this->_primaryKey;
	}

	public function setNotNull($notNull) {
		$this->_notNull = $notNull;
	}

	public function isNotNull() {
		return $this->_notNull;
	}

	public function setScale($scale) {
		$this->_scale = $scale;
	}

	public function scale() {
		return $this->_scale;
	}

	public function setComment($comment) {
		$this->_comment = $comment;
	}

	public function comment() {
		return $this->_comment;
	}

	public function setCollation($collation) {
		$this->_collation = $collation;
	}

	public function collation() {
		return $this->_collation;
	}
}

?>