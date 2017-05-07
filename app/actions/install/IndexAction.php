<?php

namespace app\actions\install;

use tea\Action;

class IndexAction extends Action {
	public function run() {
		//检查程序
		$this->data->options = []; // [ name, description, isOk, message ]
		$this->data->hasErrors = false;

		//检查PHP版本
		if (version_compare(PHP_VERSION, "7.0.0") < 0) {
			$this->_addOption("PHP7", "系统需要PHP7版本及以上才能运行", false, "当前PHP版本为" . PHP_VERSION . "，请升级您的PHP");
		}
		else {
			$this->_addOption("PHP7", "系统需要PHP7版本及以上才能运行");
		}

		//检查PDO
		if (!class_exists("\\PDO")) {
			$this->_addOption("PDO扩展", "PHP必须安装PDO才能连接数据库", false, "请安装PDO扩展");
		}
		else {
			$this->_addOption("PDO扩展", "PHP必须安装PDO才能连接数据库");
		}

		if (!in_array("mysql", pdo_drivers())) {
			$this->_addOption("pdo_mysql", "PHP必须安装PDO_MYSQL才能连接数据库", false, "请安装PDO_MYSQL扩展");
		}
		else {
			$this->_addOption("pdo_mysql", "PHP必须安装PDO_MYSQL才能连接数据库");
		}

		if (!function_exists("json_encode")) {
			$this->_addOption("json", "PHP必须安装json扩展", false, "请安装json扩展");
		}
		else {
			$this->_addOption("json", "PHP必须安装json扩展");
		}

		$dbFile = TEA_APP . "/configs/db.php";
		if (!is_writable($dbFile)) {
			$this->_addOption("数据库配置文件", "'{$dbFile}'必须可写", false, "请检查并修正'{$dbFile}'的文件写权限");
		}
		else {
			$this->_addOption("数据库配置文件", "'{$dbFile}'必须可写");
		}
	}

	private function _addOption($name, $description, $isOk = true, $message = null) {
		if (!$isOk) {
			$this->data->hasErrors = true;
		}

		$this->data->options[] = [
			"name" => $name,
			"description" => $description,
			"isOk" => $isOk,
			"message" => $message ?? "-"
		];
	}
}

?>