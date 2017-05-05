<?php

namespace es\app\actions\indice;

class CreateTypeFormAction extends BaseAction {
	public function run() {
		$this->data->serverVersion = $this->serverVersion();
	}
}

?>