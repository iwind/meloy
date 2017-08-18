<?php

namespace gateway\app\actions\server;

use app\models\server\Server;
use gateway\app\classes\ApiRequest;

/**
 * 应用主页
 */
class IndexAction extends BaseAction {
	public function run(int $serverId, string $path) {
		$server = Server::find($serverId);
		if (!$server) {
			return 404;
		}

		/** @var ApiRequest $api $api */
		$api = $server->api(ApiRequest::class);
		$response = $api->get("/@api/all");
		if (!$response->success()) {
			$this->data->success = false;
		}
		else {
			$this->data->success = true;
			$this->data->apis = $response->data();
		}

		//跳转到第一个
		if (is_empty($path) && !empty($this->data->apis)) {
			g(".index", [
				"serverId" => $serverId,
				"path" => $this->data->apis[0]->path
			]);
		}

		$this->data->path = $path;

		$this->data->api = null;
		$this->data->minutes = [];
		if (!is_empty($path) && isset($this->data->apis) && is_array($this->data->apis)) {
			foreach ($this->data->apis as $api) {
				if ($api->path == $path) {
					$this->data->api = $api;
					break;
				}
			}

			//统计数据
			$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */
			$response = $request->get("/@api/[" . $path . "]/year/" . date("Y") . "/month/" . date("m") . "/day/" . date("d"));
			$this->data->stat = $response->data();

			//调试日志
			$response = $request->get("/@api/[" . $path . "]/debug/logs");
			$this->data->debugLogs = $response->data()->logs;
		}
	}
}

?>