<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

class MonitorDataAction extends BaseAction {
	public function run() {
		$request = $this->_server->api(ApiRequest::class); /** @var ApiRequest $request */
		$response = $request->get("/@monitor");

		$this->data->data = $response->data();
	}
}

?>