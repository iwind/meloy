<?php

namespace es\app\actions\indice;

use es\api\CountApi;
use es\api\GetIndexApi;

class TypesAction extends BaseAction {
	public function run() {
		$api = $this->_server->api(GetIndexApi::class); /** @var GetIndexApi $api */
		$api->index($this->_index);

		$this->data->types = $api->get()->mappings;

		foreach ($this->data->types as $typeName => $config) {
			$countApi = $this->_server->api(CountApi::class); /** @var CountApi $countApi */
			$countApi->index($this->_index)
					->type($typeName);
			$config->count = $countApi->count();
		}

		$this->data->countTypes = count(object_keys($this->data->types));
	}
}

?>