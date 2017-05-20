<?php

namespace es\app\actions\server;

class UpdateFormAction extends BaseAction {
	public function run() {
		$options = json_decode($this->_server->options);
		$this->data->scheme = $options->scheme ?? "http";
	}
}

?>