<?php

namespace es\app\actions\server;

use es\api\cat\PluginsApi;

class PluginsAction extends BaseAction {
	public function run() {
		//取得所有插件
		$api = $this->_server->api(PluginsApi::class); /** @var PluginsApi $api */
		$api->headers("id", "name", "component", "version", "description");
		$this->data->plugins = $api->getAll();
	}
}

?>