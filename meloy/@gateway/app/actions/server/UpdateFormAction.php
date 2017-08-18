<?php

namespace gateway\app\actions\server;

class UpdateFormAction extends BaseAction {
	public function run() {
		$options = json_decode($this->_server->options);
		$this->data->scheme = $options->scheme ?? "http";
		$this->data->mockScheme = $options->mockScheme ?? "http";
		$this->data->mockHost = $options->mockHost ?? "";
	}
}

?>