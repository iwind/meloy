<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

class WatchDataAction extends BaseAction {
	public function run() {
		$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */
		$response = $request->get("/@api/watch");
		if ($response->success()) {
			$this->data->logs = $response->data();
		}
		else {
			$this->data->logs = [];
		}
	}
}

?>