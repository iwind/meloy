<?php

namespace es\app\actions\server;

use tea\Arrays;

class IndexAction extends BaseAction {
	public function run() {
		$this->data->info = Arrays::flatten($this->_api->get(""));
	}
}

?>