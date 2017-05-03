<?php

namespace es\app\actions\server;

use app\models\server\Server;
use es\API;
use es\app\actions\BaseAction;
use es\Exception;
use tea\Must;

class SaveAction extends BaseAction {
	public function run(string $name, string $host, int $port, Must $must) {
		//校验输入
		$must->field("name", $name)
			->require("请输入主机名")

			->field("host", $host)
			->require("请输入主机地址");

		if ($port < 1) {
			$this->field("port", "请输入大于1的端口")->fail();
		}

		//测试端口
		$api = new API($host, $port);
		try {
			$api->get("/", "");
		} catch (Exception $e) {
			$this->field("host", "地址和端口测试失败")->fail();
		}

		//保存
		$serverId = Server::createServer($this->userId(), Server::TYPE_ES, $name, $host, $port);

		//跳转
		$this->next(".index", [
			"serverId" => $serverId
		])->success("保存成功");
	}
}

?>