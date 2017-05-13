<?php

namespace es\app\actions\server;

use es\api\NodesApi;

class ClusterAction extends BaseAction {
	public function run() {
		//取得所有节点
		$api = $this->_server->api(NodesApi::class);/** @var NodesApi $api */
		$nodes = $api->getAll();
		$this->data->nodes = $nodes;

		//负载
		$catApi = $this->_server->api(\es\api\cat\NodesApi::class); /** @var \es\api\cat\NodesApi $catApi */
		$catApi->headers("id", "load_1m", "load_5m", "load_15m");
		$result = $catApi->getAll();

		foreach ($this->data->nodes as $id => $node) {
			foreach ($result as $data) {
				if (preg_match("/^" . $data->id . "/", $id)) {
					$node->load_1m = $data->load_1m;
				}
			}
		}
	}
}

?>