<?php

namespace redis\app\actions\server;

use app\models\server\Server;
use app\models\server\ServerType;
use redis\app\actions\BaseAction;
use tea\Must;

class SaveAction extends BaseAction {
	public function run(string $name, string $host, int $port, string $password, bool $check, Must $must) {
		//校验输入
		$must->field("name", $name)
			->require("请输入主机名")

			->field("host", $host)
			->require("请输入主机地址");

		if ($port < 1) {
			$this->field("port", "请输入大于1的端口")->fail();
		}

		//测试端口
		if ($check) {
			$redis = new \Redis();

			$bool = @$redis->connect($host, $port);

			if (!$bool) {
				$this->field("host", "连接测试失败，请重新检查")->fail();
			}
			if (!is_empty($password)) {
				if (!$redis->auth($password)) {
					$this->field("host", "连接成功，但密码校验失败")->fail();
				}
			}
		}

		//保存
		$serverTypeId = ServerType::findTypeIdWithCode("redis");
		$serverId = Server::createServer($this->userId(), $serverTypeId, $name, $host, $port, [
			"password" => $password
		]);

		//跳转
		$this->next(".index", [
			"serverId" => $serverId
		])->success("保存成功");
	}
}

?>