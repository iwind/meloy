<?php

namespace tea\db\drivers;

use tea\db\Field;

class MySQLDriver {
	private $_typeMapping = [
		"BIT" => [ Field::TYPE_INT, "l" ],
		"TINYINT" => [ Field::TYPE_INT, "l" ],
		"BOOL" => [ Field::TYPE_BOOL ],
		"BOOLEAN" => [ Field::TYPE_BOOL ],
		"SMALLINT" => [ Field::TYPE_INT, "l" ],
		"MEDIUMINT" => [ Field::TYPE_INT, "l" ],
		"INT" => [ Field::TYPE_INT, "l" ],
		"INTEGER" => [ Field::TYPE_INT, "l" ],
		"BIGINT" => [ Field::TYPE_INT, "l" ],
		"DECIMAL" => [ Field::TYPE_FLOAT, "l", "s" ],
		"DEC" => [ Field::TYPE_FLOAT, "l", "s" ],
		"FLOAT" => [ Field::TYPE_FLOAT, "l", "s" ],
		"DOUBLE" => [ Field::TYPE_DOUBLE, "l", "s" ],
		"DOUBLE PRECISION" => [ Field::TYPE_DOUBLE, "l", "s" ],
		"DATE" => [ Field::TYPE_DATE ],
		"DATETIME" => [ Field::TYPE_DATE, "s" ],
		"TIMESTAMP" => [ Field::TYPE_TIMESTAMP, "s" ],
		"TIME" => [ Field::TYPE_TIME, "s" ],
		"YEAR" => [ Field::TYPE_INT, "l" ],

		"CHAR" => [ Field::TYPE_STRING, "l" ],
		"VARCHAR" => [ Field::TYPE_STRING, "l" ],
		"BINARY" => [ Field::TYPE_STRING, "l" ],
		"VARBINARY" => [ Field::TYPE_STRING, "l" ],
		"TINYBLOB" => [ Field::TYPE_STRING ],
		"TINYTEXT" => [ Field::TYPE_STRING ],
		"BLOB" => [ Field::TYPE_LOB, "l" ],
		"TEXT" => [ Field::TYPE_STRING, "l" ],
		"MEDIUMBLOB" => [ Field::TYPE_LOB ],
		"MEDIUMTEXT" => [ Field::TYPE_STRING ],
		"LONGBLOB" => [ Field::TYPE_LOB ],
		"LONGTEXT" => [ Field::TYPE_STRING ],
		"ENUM" => [ Field::TYPE_STRING, "v" ],
		"SET" => [ Field::TYPE_STRING, "v" ],

		/** @TODO 实现GIS数据类型：http://dev.mysql.com/doc/refman/5.7/en/spatial-datatypes.html */
	];

	public function parseType($definition) {
		$pieces = explode("(", $definition);
		$code = strtoupper($pieces[0]);
		$length = 0;
		$scale = null;
		$dataType = 0;
		if (isset($this->_typeMapping[$code])) {
			$dataType = $this->_typeMapping[$code][0];
			$count = count($this->_typeMapping[$code]);
			if ($count > 1) {
				//$definition = "enum('A','B','C,','D''','E\"')";
				preg_match("/\\((.+)\\)/", $definition, $match);
				if (!empty($match)) {
					$values = array_filter(preg_split("/,/", $match[1]));//@TODO 需要实现更准确的解析
					for ($i = 1; $i < $count; $i ++) {
						$paramType = $this->_typeMapping[$code][$i];
						if ($paramType == "l") {//长度
							if (isset($values[$i - 1])) {
								$length = $values[$i - 1];
							}
						}
						else if ($paramType == "s") {//Scale
							if (isset($values[$i - 1])) {
								$scale = $values[$i - 1];
							}
						}
						else if ($paramType == "v") {//值域
							$scale = $values;
						}
					}
				}
			}
		}

		return [
			"type" => $pieces[0],
			"dataType" => $dataType,
			"scale" => $scale,
			"length" => $length
		];
	}
}

?>