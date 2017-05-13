<?php

namespace app\actions\install;

use app\models\server\ServerType;
use app\models\user\User;
use tea\Must;
use tea\string\Helper;

class CreateDbAction extends BaseAction {
	public function run(string $host, string $port, string $username, string $password, string $dbname, string $prefix, Must $must) {
		$host = preg_replace("/\\s+/", "", $host);
		$port = preg_replace("/\\s+/", "", $port);
		$dbname = preg_replace("/\\s+/", "", $dbname);
		$prefix = preg_replace("/\\s+/", "", $prefix);

		//检查参数
		$must->field("host", $host)
			->require("请输入数据库主机地址")

			->field("port", $port)
			->require("请输入数据库端口")

			->field("username", $username)
			->require("请输入数据库连接用户名")

			->field("dbname", $dbname)
			->require("请输入数据库名称");

		//检查连接
		$pdo = null;
		try {
			$pdo = new \PDO("mysql:dbname={$dbname};host={$host};port={$port};charset=utf8", $username, $password);
		} catch (\Exception $e) {
			if (preg_match("/Connection/i", $e->getMessage())) {
				$this->fail("无法连接数据库 '{$host}:{$port}'");
			}
			if (preg_match("/Access denied/i", $e->getMessage())) {
				$this->fail("用户'{$username}'（密码'{$password}'）没有权限访问数据库");
			}
			if (preg_match("/Unknown database/i", $e->getMessage())) {
				$this->fail("数据库'{$dbname}'不存在");
			}

			$this->fail($e->getMessage());
		}

		//写入数据库配置
		$dbFile = TEA_APP . "/configs/db.php";
		$dbTemplateFile = TEA_APP . "/configs/db.template.php";
		$contents = file_get_contents($dbTemplateFile);
		$contents = str_replace("%{prefix}", $prefix, $contents);
		$contents = str_replace("%{host}", $host, $contents);
		$contents = str_replace("%{port}", $port, $contents);
		$contents = str_replace("%{username}", $username, $contents);
		$contents = str_replace("%{password}", $password, $contents);
		$contents = str_replace("%{dbname}", $dbname, $contents);
		$contents = str_replace("%{secret}", Helper::randomString(), $contents);

		//写入文件
		$result = @file_put_contents($dbFile, $contents);
		if (!$result) {
			$this->fail("数据库配置文件写入失败，请检查'{$dbFile}'的写权限");
		}

		if (function_exists("opcache_invalidate")) {
			opcache_invalidate($dbFile);
		}

		//创建表
		$sql = file_get_contents(TEA_ROOT . DS . "install/install.sql");
		$sql = preg_replace("/`pp_/", "`" . $prefix, $sql);

		try {
			$pdo->exec($sql);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		//创建用户
		if (!User::exist(1)) {
			User::createUser("root@meloy.cn", "123456", "管理员");
		}

		//创建主机类型
		if (!ServerType::existServerType("es")) {
			ServerType::createServerType("ES搜索", "es");
		}
		if (!ServerType::existServerType("mongo")) {
			ServerType::createServerType("MongoDB", "mongo", ServerType::STATE_DISABLED);
		}
		if (!ServerType::existServerType("redis")) {
			ServerType::createServerType("Redis", "redis");
		}

		//登录
		$this->auth()->store("install", 1);

		$this->next(".finish");
	}
}

?>