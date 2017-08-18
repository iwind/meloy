<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

/**
 * 发起API请求
 */
class RequestAction extends BaseAction {
	public function run(string $path, string $query, int $host, string $method, string $headers, string $body) {
		$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */
		$response = $request->exec("/@api/[" . $path . "]/request/host/" . $host . "?" . $query, $method, $headers, $body);
		if ($response->success()) {
			$this->data->body = $response->data();
		}
		else {
			$this->data->body = "";
		}
	}
}

?>