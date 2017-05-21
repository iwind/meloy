<?php

namespace es\app\actions\indice;

use es\api\GetIndexApi;
use es\api\StatsApi;

class IndexAction extends BaseAction {
	public function run() {
		//基本信息
		$api = $this->_server->api(GetIndexApi::class);/** @var GetIndexApi $api */
		$api->index($this->_index);
		$info = $api->get();
		$this->data->info = $info;
		$this->data->countTypes = count(object_keys($info->mappings));
		$this->data->countAliases = count(object_keys($info->aliases));

		//统计信息
		$api = $this->_server->api(StatsApi::class); /** @var StatsApi $api */
		$api->index($this->_index);
		$stats = $api->get();

		$this->data->stats = $stats;
	}
}

?>