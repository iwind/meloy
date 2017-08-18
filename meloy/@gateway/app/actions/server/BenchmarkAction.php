<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

/**
 * 基准测试
 */
class BenchmarkAction extends BaseAction {
	public function before() {
		parent::before();

		session_write_close();
	}

	public function run(string $path, string $query, int $host, string $method, string $headers, string $body, int $requests, int $concurrency) {
		$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */
		$request->timeout(30);
		$response = $request->exec("/@api/[" . $path . "]/benchmark/host/" . $host . "/requests/" . $requests . "/concurrency/" . $concurrency . "?" . $query, $method, $headers, $body);

		if ($response->success()) {
			$this->data->result = $response->data();
		}
		else {
			$this->data->result = null;
		}
	}
}

?>