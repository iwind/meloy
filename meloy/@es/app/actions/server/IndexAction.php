<?php

namespace es\app\actions\server;

use es\api\GetIndexApi;
use es\Exception;
use tea\Arrays;

class IndexAction extends BaseAction {
	public function run() {
		/**
		 * @var GetIndexApi $api
		 */
		$api = $this->_server->api(GetIndexApi::class);

		$this->data->error = null;
		try {
			$this->data->info = Arrays::flatten($api->get());
		} catch (Exception $e) {
			$this->data->error = $e->getMessage();
		}
	}
}

?>