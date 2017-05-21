<?php

namespace es\app\actions\server;

use es\api\cat\NodesApi;

class MonitorAction extends BaseAction {
	public function run(string $nodeId) {
		//ES版本号
		$this->data->serverVersion = $this->serverVersion();

		//所有节点
		$api = $this->_server->api(NodesApi::class); /** @var NodesApi $api */
		$api->headers("id,name,pid,ip,port,load_*,heap.*,ram.*,search.*");
		$nodes = $api->getAll();
		$this->data->nodes = $nodes;

		//当前选中的节点
		if (!is_empty($nodeId)) {
			foreach ($nodes as $node) {
				if (preg_match("/^" . preg_quote($node->id, "/") . "/", $nodeId)) {
					$this->data->selectedNodeId = $node->id;
					break;
				}
			}
		}
	}
}

?>