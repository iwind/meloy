<?php

namespace es\app\actions\type;

use es\api\GetMappingApi;

class AddDocFormAction extends BaseAction {
	public function run() {
		$api = $this->_server->api(GetMappingApi::class); /** @var GetMappingApi $api */
		$api->index($this->_index)
			->type($this->_type);
		$this->data->fields = $api->get()->properties;
	}
}

?>