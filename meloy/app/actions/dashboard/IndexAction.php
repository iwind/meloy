<?php

namespace app\actions\dashboard;

use app\models\server\Server;
use app\models\server\ServerType;
use app\specs\ModuleSpec;

/**
 * 控制台
 */
class IndexAction extends BaseAction {
	public function run() {
		$modules = ModuleSpec::findAllVisibleModulesForUser($this->userId());
		$serverTypes = [];
		foreach ($modules as $module) {
			foreach ($module->serverTypes() as $serverType) {
				if (!in_array($serverType, $serverTypes)) {
					$serverTypes[] = $serverType;
				}
			}
		}

		$servers = [];
		foreach ($serverTypes as $serverType) {
			$serverTypeId = ServerType::findTypeIdWithCode($serverType);
			if ($serverTypeId == 0) {
				continue;
			}
			foreach (Server::findUserServersWithType($this->userId(), $serverTypeId) as $server) {
				$servers[] = (object)[
					"id" => $server->id,
					"name" => $server->name,
					"host" => $server->host,
					"port" => $server->port,
					"module" => "@" . $server->typeName(),
					"typeName" => $server->typeName()
				];
			}
		}

		$this->data->servers = $servers;
	}
}

?>