<?php

namespace tea\db\jobs;


use tea\db\Db;
use tea\db\Field;
use tea\db\Table;
use tea\Job;

/**
 * 对比数据库
 *
 * @package tea\db\commands
 */
class CompareJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return "compare";
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		return "Compare current database with another";
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		return "Compare current database with another";
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "tea db:compare [DB ID1] [User:Password@][DB ID2]";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		if (!isset($_SERVER["argv"][2])) {
			$this->output("<warn>Usage: " . $this->stub() . "</warn>\n");
			return;
		}

		if (isset($_SERVER["argv"][3])) {
			$defaultDbId = $_SERVER["argv"][2];
			$dbId = $_SERVER["argv"][3];
		}
		else {
			$defaultDbId = o("db.default.db");
			$dbId = $_SERVER["argv"][2];
		}
		if ($dbId == $defaultDbId) {
			$this->output("<error>'{$dbId}' is default db</error>\n");
			return;
		}
		$this->println("compare '" . $defaultDbId . "' and '" . $dbId . "' ...");

		$error = 0;

		$compare = o("db.compare");
		if (!is_array($compare) || empty($compare)) {
			$compare = [
				"charset", "events", "indexes", "triggers", "functions", "procedures"
			];
		}


		$defaultDbOptions = $this->_parseDbId($defaultDbId);
		$defaultDbId = $defaultDbOptions["id"];
		$defaultDb = Db::db($defaultDbId, false);
		if (!is_null($defaultDbOptions["user"])) {
			$config = $defaultDb->config();
			$config["username"] = $defaultDbOptions["user"];
			$defaultDb->setConfig($config);
		}
		if (!is_null($defaultDbOptions["pass"])) {
			$config = $defaultDb->config();
			$config["password"] = $defaultDbOptions["pass"];
			$defaultDb->setConfig($config);
		}
		$defaultTables = $defaultDb->listTables();
		$defaultTableNames = array_map(function (Table $table) {
			return $table->name();
		}, $defaultTables);

		//另外一个数据库
		$anotherDbOptions = $this->_parseDbId($dbId);
		$anotherDb = Db::db($anotherDbOptions["id"], false);

		if (!is_null($anotherDbOptions["user"])) {
			$config = $anotherDb->config();
			$config["username"] = $anotherDbOptions["user"];
			$anotherDb->setConfig($config);
		}
		if (!is_null($anotherDbOptions["pass"])) {
			$config = $anotherDb->config();
			$config["password"] = $anotherDbOptions["pass"];
			$anotherDb->setConfig($config);
		}

		$anotherTables = $anotherDb->listTables();
		$anotherTableNames = array_map(function (Table $table) {
			return $table->name();
		}, $anotherTables);

		//Table checking
		foreach ($defaultTableNames as $index => $defaultTableName) {
			if (!in_array($defaultTableName, $anotherTableNames)) {
				$error ++;

				$this->output("  <warn>+ table " . $defaultTableName . "</warn>\n");
			}
			else {
				/* @var \tea\db\Table $defaultTable */
				$defaultTable = $defaultTables[$index];
				$defaultFields = $defaultTable->fields();
				$defaultFieldNames = array_map(function (Field $field) {
					return $field->name();
				}, $defaultFields);

				/* @var \tea\db\Table $anotherTable */
				$anotherIndex = array_keys($anotherTableNames, $defaultTableName)[0];
				$anotherTable = $anotherTables[$anotherIndex];
				$anotherFields = $anotherTable->fields();
				$anotherFieldNames = array_map(function (Field $field) {
					return $field->name();
				}, $anotherFields);

				//对比字符集
				if (in_array("charset", $compare) && $defaultTable->collation() != $anotherTable->collation()) {
					$this->output("  <warn>* table " . $defaultTableName . " charset " . $anotherTable->collation() . " -> " . $defaultTable->collation() . "</warn>\n");
				}

				foreach ($defaultFields as $defaultField) {
					if (!in_array($defaultField->name(), $anotherFieldNames)) {
						$error ++;

						$this->output("  <warn>+ field " . $defaultTableName . "." . $defaultField->name() . " " . $defaultField->fullType() . " DEFAULT " . var_export($defaultField->defaultValue(), true)  . " COMMENT '" . addslashes($defaultField->comment()) . "'</warn>\n");
					}
					else {
						$fieldIndex = array_keys($anotherFieldNames, $defaultField->name())[0];
						$anotherField = $anotherFields[$fieldIndex];

						//对比字符集
						if (in_array("charset", $compare) && $defaultField->collation() != $anotherField->collation()) {
							$error ++;

							$this->output("  <warn>* change " . $defaultTableName . "." . $defaultField->name() . " charset " . $anotherField->collation() . " -> " . $defaultField->collation() . "</warn>\n");
						}

						//对比字段定义
						if ($defaultField->fullType() !== $anotherField->fullType() || $defaultField->defaultValue() != $anotherField->defaultValue()) {
							$error ++;

							$default1 = $defaultField->defaultValue();
							$default2 = $anotherField->defaultValue();
							if (!is_null($default1)) {
								if (is_string($default1)) {
									$default1 = "\"" . $default1 . "\"";
								}
								$default1 = " DEFAULT " . $default1  . " COMMENT '" . addslashes($defaultField->comment()) . "'";
							}
							if (!is_null($default2)) {
								if (is_string($default2)) {
									$default2 = "\"" . $default2 . "\"";
								}
								$default2 = " DEFAULT " . $default2  . " COMMENT '" . addslashes($anotherField->comment()) . "'";
							}

							$this->output("  <warn>* change " . $defaultTableName . "." . $defaultField->name() . " " . $anotherField->fullType() . "{$default2} -> " . $defaultField->fullType() . "{$default1} </warn>\n");
						}
					}
				}

				foreach ($anotherFieldNames as $anotherFieldName) {
					if (!in_array($anotherFieldName, $defaultFieldNames)) {
						$error ++;
						$this->output("  <warn>- field " . $defaultTableName . "." . $anotherFieldName . "</warn>\n");
					}
				}

				//检查Indexes
				if (in_array("indexes", $compare)) {
					$currentIndexes = [];
					foreach ($defaultDb->findAll("SHOW INDEXES FROM {$defaultTableName}") as $index) {
						unset($index["Comment"]);
						unset($index["Index_comment"]);
						unset($index["Cardinality"]);
						$json = json_encode($index, JSON_PRETTY_PRINT);
						//$json = preg_replace("{\"Cardinality\": \"\\d+\",}", "\"Cardinality\": \"1\",", $json);
						$currentIndexes[$index["Key_name"]] = $json;
					}

					$remoteIndexes = [];
					foreach ($anotherDb->findAll("SHOW INDEXES FROM {$defaultTableName}") as $index) {
						unset($index["Comment"]);
						unset($index["Index_comment"]);
						unset($index["Cardinality"]);
						$json = json_encode($index, JSON_PRETTY_PRINT);
						//$json = preg_replace("{\"Cardinality\": \"\\d+\",}", "\"Cardinality\": \"1\",", $json);
						$remoteIndexes[$index["Key_name"]] = $json;
					}
					foreach ($currentIndexes as $keyName => $definition) {
						if (isset($remoteIndexes[$keyName])) {
							if ($remoteIndexes[$keyName] != $definition) {
								$this->output("  <warn>* index " . $defaultTableName . "." . $keyName . $this->_paddingTab("~~~\n" . $definition . "\n~~~") . "\n</warn>\n");
							}
						}
						else {
							$this->output("  <warn>+ index " . $defaultTableName . "." . $keyName . $this->_paddingTab("~~~\n" . $definition . "\n~~~") . "\n</warn>\n");
						}
					}
					foreach ($remoteIndexes as $keyName => $definition) {
						if (!isset($currentIndexes[$keyName])) {
							$this->output("  <warn>- index " . $defaultTableName . "." . $keyName . $this->_paddingTab("~~~\n" . $definition . "\n~~~") . "\n</warn>\n");
						}
					}
				}

				//检查Triggers
				if (in_array("triggers", $compare)) {
					$currentTriggers = [];
					foreach ($defaultDb->findAll("SHOW TRIGGERS LIKE '{$defaultTableName}'") as $trigger) {
						unset($trigger["Definer"], $trigger["character_set_client"], $trigger["collation_connection"],
							$trigger["Collation"], $trigger["Database Collation"], $trigger["sql_mode"]);
						$statment = $trigger["Statement"];
						$trigger["Statement"] = "tea_placeholder_statement";
						$json = $this->_paddingTab("~~~\n" . json_encode($trigger, JSON_PRETTY_PRINT) . "\n~~~");
						$json = str_replace($trigger["Statement"], $statment, $json);
						$currentTriggers[$trigger["Trigger"]] = $json;
					}

					$remoteTriggers = [];
					foreach ($anotherDb->findAll("SHOW TRIGGERS LIKE '{$defaultTableName}'") as $trigger) {
						unset($trigger["Definer"], $trigger["character_set_client"], $trigger["collation_connection"],
							$trigger["Collation"], $trigger["Database Collation"], $trigger["sql_mode"]);
						$statment = $trigger["Statement"];
						$trigger["Statement"] = "tea_placeholder_statement";
						$json = $this->_paddingTab("~~~\n" . json_encode($trigger, JSON_PRETTY_PRINT) . "\n~~~");
						$json = str_replace($trigger["Statement"], $statment, $json);
						$remoteTriggers[$trigger["Trigger"]] = $json;
					}
					foreach ($currentTriggers as $keyName => $definition) {
						if (isset($remoteTriggers[$keyName])) {
							if ($remoteTriggers[$keyName] != $definition) {
								$this->output("  <warn>* trigger " . $defaultTableName . "." . $keyName . $definition . "\n</warn>\n");
							}
						}
						else {
							$this->output("  <warn>+ trigger " . $defaultTableName . "." . $keyName . $definition . "\n</warn>\n");
						}
					}
					foreach ($remoteTriggers as $keyName => $definition) {
						if (!isset($currentTriggers[$keyName])) {
							$this->output("  <warn>- trigger " . $defaultTableName . "." . $keyName . $definition . "\n</warn>\n");
						}
					}
				}

				//@TODO 检查Events
				if (in_array("events", $compare)) {

				}

				//@TODO 检查FUNCTION
				if (in_array("functions", $compare)) {

				}

				//@TODO 检查PROCEDURE
				if (in_array("procedures", $compare)) {

				}

			}
		}
		foreach ($anotherTableNames as $anotherTableName) {
			if (!in_array($anotherTableName, $defaultTableNames)) {
				$error ++;

				$this->output("  <warn>- table " . $anotherTableName . "</warn>\n");
			}
		}

		if ($error == 0) {
			$this->println("<ok>Both databases have save same schema</ok>");
		}
		else {
			$this->println("There are <warn>" . $error . "</warn> issues to be fixed");
		}
	}

	private function _paddingTab($string) {
		$result = "";
		foreach (explode("\n", $string) as $piece) {
			$result .= "\n" . str_repeat(" ", 10) . $piece;
		}
		return $result;
	}

	private function _parseDbId($dbId) {
		$pieces = preg_split("/@/", $dbId, 2);
		if (count($pieces) == 1) {
			return [
				"user" => null,
				"pass" => null,
				"id" => $dbId
			];;
		}
		$dbId = $pieces[1];
		$userPieces = explode(":", $pieces[0]);
		if (count($userPieces) == 1) {
			return [
				"user" => trim($userPieces[0]),
				"pass" => null,
				"id" => $dbId
			];
		}
		return [
			"user" => trim($userPieces[0]),
			"pass" => trim($userPieces[1]),
			"id" => $dbId
		];
	}
}

?>