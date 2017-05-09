<?php

namespace tea\db\jobs;

use tea\Job;

/**
 * Class InfoCommand
 *
 * @package tea\db\commands
 */
class InfoJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return "info";
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		return "Show database infomation";
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		return "Show database infomation";
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "tea :db.info";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		if (!extension_loaded("pdo_mysql")) {
			$this->output("<error>'pdo_mysql' extension should be loaded before you start</error>\n");
			return;
		}

		$config = o(":db");

		if (isset($config["dbs"])) {
			$dbs = $config["dbs"];
			foreach ($dbs as $dbId => $dbConfig) {
				if ($dbId != o(":db.default.db")) {
					unset($config["dbs"][$dbId]);
				}
			}
		}
		$this->output("info in JSON format: ... \n===\n<code>" . json_encode($config, JSON_PRETTY_PRINT) . "</code>\n===\n");
	}
}

?>