<?php

namespace es\app\actions\server;

use es\api\GetIndexApi;
use tea\Arrays;

class IndexAction extends BaseAction {
	public function run() {
		/**
		 * @var GetIndexApi $api
		 */
		$api = $this->_server->api(GetIndexApi::class);
		$this->data->info = Arrays::flatten($api->get());
	}
}

?>