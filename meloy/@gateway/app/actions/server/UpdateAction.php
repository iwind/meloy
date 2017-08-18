<?php

namespace gateway\app\actions\server;

use app\models\server\Server;
use gateway\app\classes\ApiRequest;
use tea\Must;

class UpdateAction extends BaseAction {
	public function run(string $name, string $scheme, string $host, string $mockScheme, string $mockHost, bool $check, Must $must) {
		$info = parse_url($host);
		$host = $info["host"] ?? "";
		$port = $info["port"] ?? 80;

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
			$api = new ApiRequest();
			$api->prefix("{$scheme}://" . $host . ":" . $port);
			$response = $api->get("/@api");

			if (!$response->success()) {
				$this->field("host", "地址和端口测试失败，请重新检查")->fail();
			}
		}

		//保存
		Server::updateServer($this->_server->id, $name, $host, $port, [
			"scheme" => $scheme,
			"mockScheme" => $mockScheme,
			"mockHost" => $mockHost,
		]);

		//跳转
		$this->refresh()->success("保存成功");
	}
}

?>