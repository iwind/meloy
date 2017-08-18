<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\server\Server;
use app\models\server\ServerType;
use app\specs\ModuleSpec;

/**
 * 权限设置 > 服务器
 */
class PermissionServersAction extends BaseAction {
	public function run(string $module) {
		$this->validateAdmin();

		$spec = ModuleSpec::new($module);

		$servers = [];
		foreach ($spec->serverTypes() as $serverType) {
			$typeId = ServerType::findTypeIdWithCode($serverType);
			if ($typeId == 0) {
				continue;
			}
			foreach (Server::findAllServersWithType($typeId) as $server) {
				$servers[] = $server->asPlain([ "id", "name", "host", "port" ]);
			}
		}

		$this->data->servers = $servers;
	}
}

?>