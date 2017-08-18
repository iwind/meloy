<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

/**
 * 统计
 */
class StatAction extends BaseAction {
	public function run() {
		$this->data->error = "";

		$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */
		$response = $request->get("/@api/stat");
		if ($response->success()) {
			$this->data->stat = $response->data();
			$this->data->stat->days = ceil((time() - strtotime($this->data->stat->dateFrom)) / 86400);
		}
		else {
			$this->data->error = "暂时无法读取统计数据。";
		}
	}
}

?>