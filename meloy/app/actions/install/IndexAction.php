<?php

namespace app\actions\install;

class IndexAction extends BaseAction {
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

		//检查pdo_mysql
		if (!in_array("mysql", pdo_drivers())) {
			$this->_addOption("pdo_mysql", "PHP必须安装pdo_mysql才能连接数据库", false, "请安装pdo_mysql扩展");
		}
		else {
			$this->_addOption("pdo_mysql", "PHP必须安装pdo_mysql才能连接数据库");
		}

		//检查curl
		if (!function_exists("curl_init")) {
			$this->_addOption("curl", "PHP必须安装curl扩展", false, "请安装curl扩展");
		}
		else {
			$this->_addOption("curl", "PHP必须安装curl扩展");
		}

		//检查json
		if (!function_exists("json_encode")) {
			$this->_addOption("json", "PHP必须安装json扩展", false, "请安装json扩展");
		}
		else {
			$this->_addOption("json", "PHP必须安装json扩展");
		}

		//检查数据库配置文件
		$dbFile = TEA_APP . DS . "configs" . DS . "db.php";
		if (is_file($dbFile)) {
			if (!is_writable($dbFile)) {
				$this->_addOption("数据库配置文件", "'{$dbFile}'必须可写", false, "请检查并修正'{$dbFile}'的文件写权限");
			}
			else {
				$this->_addOption("数据库配置文件", "'{$dbFile}'必须可写");
			}
		}
		else {
			$dbDir = dirname($dbFile);
			if (!is_writable($dbDir) || !@copy($dbDir . DS . "db.template.php", $dbFile)) {
				$this->_addOption("数据库配置所在目录", "'{$dbDir}'必须可写", false, "请检查并修正'{$dbDir}'的文件写权限");
			}
			else {
				$this->_addOption("数据库配置所在目录", "'{$dbDir}'必须可写");
			}
		}

		//检查临时目录
		$tmp = TEA_ROOT . DS . "tmp";
		if (!is_writable($tmp)) {
			$this->_addOption("临时目录", "'{$tmp}'必须可写", false, "请检查并修正'{$tmp}'的文件写权限");
		}
		else {
			$this->_addOption("临时目录", "'{$tmp}'必须可写");
		}

		//检查模板临时目录
		$tplTmpDir = TEA_ROOT . DS . "tmp" . DS . "tpl";
		if (!is_writable($tplTmpDir) && !@make_dir($tplTmpDir)) {
			$this->_addOption("模板临时目录", "'{$tplTmpDir}'必须可写", false, "请检查并修正'{$tplTmpDir}'的文件写权限");
		}
		else {
			$this->_addOption("模板临时目录", "'{$tplTmpDir}'必须可写");
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