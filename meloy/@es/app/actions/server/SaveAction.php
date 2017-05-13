<?php

namespace es\app\actions\server;

use app\models\server\Server;
use app\models\server\ServerType;
use es\api\Api;
use es\app\actions\BaseAction;
use es\Exception;
use tea\Must;

class SaveAction extends BaseAction {
	public function run(string $name, string $host, int $port, bool $check, Must $must) {
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
			$api = new Api();
			$api->prefix("http://" . $host . ":" . $port);
			$api->endPoint("/");

			try {
				$api->sendGet();
			} catch (Exception $e) {
				$this->field("host", "地址和端口测试失败，请重新检查")->fail();
			}
		}

		//保存
		$serverTypeId = ServerType::findTypeIdWithCode("es");
		$serverId = Server::createServer($this->userId(), $serverTypeId, $name, $host, $port);

		//跳转
		$this->next(".index", [
			"serverId" => $serverId
		])->success("保存成功");
	}
}

?>