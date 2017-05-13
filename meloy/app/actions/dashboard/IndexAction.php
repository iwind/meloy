<?php

namespace app\actions\dashboard;

use app\models\server\Server;

class IndexAction extends BaseAction {
	public function run() {
		$servers = Server::findAllUserServers($this->userId());
		$this->data->servers = array_map(function (Server $server) {
			return (object)[
				"id" => $server->id,
				"name" => $server->name,
				"host" => $server->host,
				"port" => $server->port,
				"module" => $server->module(),
				"typeName" => $server->typeName()
			];
		}, $servers);
	}
}

?>