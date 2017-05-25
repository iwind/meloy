<?php

namespace redis\app\actions\server;

use app\models\server\Server;
use tea\Must;

class UpdateAction extends BaseAction {
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
		Server::updateServer($this->_server->id, $name, $host, $port, [
			"password" => $password
		]);

		//跳转
		$this->next(".index", [
			"serverId" => $this->_server->id
		])->success("保存成功");
	}
}

?>