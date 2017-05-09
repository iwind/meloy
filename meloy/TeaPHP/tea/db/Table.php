<?php


namespace tea\db;

/**
 * 数据表类
 *
 * @package tea\db
 */
class Table {
	private $_name;
	private $_fields = [];
	private $_comment;
	private $_collation;

	/**
	 * @var Db
	 */
	private $_db;

	private $_info = [];

	public function __construct() {

	}

	public function setName($name) {
		$this->_name = $name;
	}

	public function name() {
		return $this->_name;
	}

	public function setDb(Db $db) {
		$this->_db = $db;
	}

	public function retrieveInfo() {
		$dsn = $this->_db->dsn();
		if (!empty($dsn)) {
			$this->_info = $this->_db->findOne("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='{$dsn[1]['dbname']}' AND table_name='{$this->_name}'");

			if (isset($this->_info["TABLE_COMMENT"])) {
				$this->_comment = $this->_info["TABLE_COMMENT"];
				$this->_collation = $this->_info["TABLE_COLLATION"];
			}
		}
	}

	public function comment() {
		return $this->_comment;
	}

	public function setComment($comment) {
		$this->_comment = $comment;
	}

	public function collation() {
		return $this->_collation;
	}

	/**
	 * 取得所有字段
	 *
	 * @return Field[]
	 */
	public function fields() {
		if (!empty($this->_fields)) {
			return $this->_fields;
		}

		$ones = $this->_db->query()->sql("SHOW FULL COLUMNS FROM `{$this->_name}`")->findAll();

		foreach ($ones as $one) {
			$field = new Field();
			$field->setName($one["Field"]);
			$field->setNotNull($one["Null"] == "NO");
			$field->setPrimaryKey($one["Key"] == "PRI");
			$field->setAutoIncrement(preg_match("/auto_increment/i", $one["Extra"]));
			$field->setDefaultValue($one["Default"]);
			$field->setComment($one["Comment"]);
			$field->setCollation($one["Collation"]);

			//分析类型和长度
			$this->_parseField($field, $one["Type"]);

			$this->_fields[] = $field;
		}

		return $this->_fields;
	}

	private function _parseField(Field $field, $definition) {
		$field->setFullType($definition);
		$data = $this->_db->driver()->parseType($definition);
		$field->setType($data["type"]);
		$field->setDataType($data["dataType"]);
		$field->setLength($data["length"]);
		$field->setScale($data["scale"]);
	}
}

?>