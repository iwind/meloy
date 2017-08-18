<?php

namespace gateway\app\actions\server;

use gateway\app\classes\ApiRequest;

/**
 * 排行
 */
class RanksAction extends BaseAction {
	public function run() {
		$request = $this->_server->api(ApiRequest::class);/** @var ApiRequest $request */
		$response = $request->get("/@api/stat/requests/rank");
		if ($response->success()) {
			$this->data->requests = $response->data();
		}
		else {
			$this->data->requests = [];
		}

		$response = $request->get("/@api/stat/hits/rank");
		if ($response->success()) {
			$this->data->hits = array_map(function ($data) {
				$data->percent = sprintf("%.2f", $data->percent);
				return $data;
			}, $response->data());
		}
		else {
			$this->data->hits = [];
		}

		$response = $request->get("/@api/stat/errors/rank");
		if ($response->success()) {
			$this->data->errors = array_map(function ($data) {
				$data->percent = sprintf("%.2f", $data->percent);
				return $data;
			}, $response->data());
		}
		else {
			$this->data->errors = [];
		}

		$response = $request->get("/@api/stat/cost/rank");
		if ($response->success()) {
			$this->data->costs = $response->data();
		}
		else {
			$this->data->costs = [];
		}
	}
}

?>