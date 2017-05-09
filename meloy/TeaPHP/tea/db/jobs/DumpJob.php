<?php

namespace tea\db\jobs;

use tea\Job;

/**
 * Class DumpCommand
 *
 * bin/tea :db.dump [DB ID] --no-data --result-file=[RESULT FILE]
 *
 * @package tea\db\commands
 */
class DumpJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return [ "dump", "schema" ];
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		return "Dump database";
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		return "Dump database";
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "tea :db.dump [DB ID] [options ...]\ntea :db.schema";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		$mysql = get_cfg_var("tea.mysql");
		if (!$mysql) {
			$this->println("<error>'tea.mysql' should be configured in php.ini</error>");
		}

		$args = $_SERVER["argv"];
		$dbId = null;
		if (isset($args[2])) {
			$dbId = $args[2];
		}
		else {
			$dbId = o("db.default.db");
		}

		$options = o("db.dbs.{$dbId}");
		if (!is_array($options)) {
			$this->println("<error>No database with id '{$dbId}'</error>");
			return;
		}

		$username = $options["username"];
		$password = $options["password"];

		$dsn = $this->_parseDSN($options["dsn"]);
		$host = $dsn["options"]["host"];
		$port = isset($dsn["options"]["port"]) ? $dsn["options"]["port"] : null;
		$dbname = $dsn["options"]["dbname"];
		$charset =  isset($dsn["options"]["charset"]) ? $dsn["options"]["charset"] : null;

		$cmd = $mysql . "/bin/mysqldump {$dbname} -h{$host}";
		if ($port) {
			$cmd .= " --port={$port}";
		}
		if ($username) {
			$cmd .= " -u{$username}";
		}
		if ($password) {
			$cmd .= " -p{$password}";
		}
		if ($charset) {
			$cmd .= " --default-character-set={$charset}";
		}
		$argc = $_SERVER["argc"];
		if ($argc > 2) {
			for ($i = 3; $i < $argc; $i ++) {
				$cmd .= " " . $_SERVER["argv"][$i];
			}
		}

		if ($this->subCode() == "schema") {
			if (!preg_match("/\\s--no-data=/", $cmd)) {
				$cmd .= " --no-data";
			}
		}

		if (!preg_match("/\\s--result-file=/", $cmd)) {
			$this->println("<code>dump to /data/dump.sql ...</code>");
			$cmd .= " --result-file=" . TEA_ROOT . "/data/dump.sql";
		}

		$this->exec($cmd);
		$this->println("<ok>finished</ok>");
	}

	private function _parseDSN($dsn) {
		list($driver, $options) = explode(":", $dsn, 2);
		$optionsArray = [];
		$options = str_replace(";", "&", $options);
		parse_str($options, $optionsArray);
		return [
			"driver" => $driver,
			"options" => $optionsArray
		];
	}
}

?>