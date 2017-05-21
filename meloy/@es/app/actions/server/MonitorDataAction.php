<?php

namespace es\app\actions\server;

use es\api\cat\NodesApi;

class MonitorDataAction extends BaseAction {
	public function run(string $nodeId) {
		//所有节点
		$api = $this->_server->api(NodesApi::class); /** @var NodesApi $api */

		$is2_x = false;
		if (version_compare($this->serverVersion(), "5.0.0") < 0) {
			$is2_x = true;
			$api->headers("id,name,pid,ip,port,load,heap.*,ram.*,search.*");
		}
		else {
			$api->headers("id,name,pid,ip,port,load_*,heap.*,ram.*,cpu,search.*");
		}
		$nodes = $api->getAll();

		unset($this->data);
		$this->data = new \stdClass();
		foreach ($nodes as $node) {
			if ($is2_x) {
				$node->load_1m = $node->load;
			}

			if ($node->id == $nodeId) {
				$this->data = $node;
			}
		}
	}
}

?>