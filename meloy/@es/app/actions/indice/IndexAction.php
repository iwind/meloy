<?php

namespace es\app\actions\indice;

use es\api\GetMappingApi;
use es\api\StatsApi;

class IndexAction extends BaseAction {
	public function run() {
		//类型数量
		$api = $this->_server->api(GetMappingApi::class);/** @var GetMappingApi $api */
		$api->index($this->_index);
		$this->data->countTypes = count(object_keys($api->getAll()));

		//统计信息
		/** @var StatsApi $api */
		$api = $this->_server->api(StatsApi::class);
		$api->index($this->_index);
		$stats = $api->get();

		$this->data->stats = $stats;
	}
}

?>