<?php

namespace tea\db\jobs;

use tea\Job;
use tea\string\Helper;

class SecretJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return "secret";
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		return "Generate secret";
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		return "Generate secret";
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "tea :db.secret [LENGTH]";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		$args = $_SERVER["argv"];
		$length = 32;
		if (isset($args[2])) {
			$length2 = intval($args[2]);
			if ($length2 > 0) {
				$length = $length2;
			}
		}
		$secret = Helper::randomString($length);
		$this->output("<ok>{$secret}</ok>\n");
	}
}

?>