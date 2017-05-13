<?php

namespace redis\app\actions\server;

class UpdateFormAction extends BaseAction {
	public function run() {
		$this->data->server->options = json_decode($this->_server->options);
	}
}

?>