<?php

namespace gateway\app\actions\server;

use app\models\server\Server;
use app\models\server\ServerType;
use gateway\app\actions\BaseAction;
use gateway\app\classes\ApiRequest;
use tea\Must;

class SaveAction extends BaseAction {
	public function run(string $name, string $scheme, string $host, string $mockScheme, string $mockHost, bool $check, Must $must) {
		$info = parse_url("{$scheme}://" . $host);
		$host = $info["host"] ?? "";
		$port = $info["port"] ?? 80;

		//校验输入
		$must->field("name", $name)
			->require("请输入应用名")

			->field("host", $host)
			->require("请输入主机地址");

		if ($port < 1) {
			$this->field("port", "请输入大于1的端口")->fail();
		}

		//测试端口
		if ($check) {
			$request = new ApiRequest();
			$response = $request->get("{$scheme}://" . $host . ":" . $port . "/@api");
			if (!$response->success()) {
				$this->field("host", "地址和端口测试失败，请重新检查")->fail();
			}
		}

		//保存
		$serverTypeId = ServerType::findTypeIdWithCode("gateway");
		$serverId = Server::createServer($this->userId(), $serverTypeId, $name, $host, $port, [
			"scheme" => $scheme,
			"mockScheme" => $mockScheme,
			"mockHost" => $mockHost
		]);

		//跳转
		$this->next(".index", [
			"serverId" => $serverId
		])->success("保存成功");
	}
}

?>