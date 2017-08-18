<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

/**
 * 调试日志
 */
class DebugLogsAction extends BaseAction {
	public function run(string $path) {
		$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */

		//刷新日志
		$request->get("/@api/[" . $path . "]/debug/flush");

		//调试日志
		$response = $request->get("/@api/[" . $path . "]/debug/logs");
		$this->data->debugLogs = $response->data()->logs;
	}
}

?>