<?php

namespace gateway\app\actions\index;

use app\models\server\Server;
use app\models\server\ServerType;
use gateway\app\actions\BaseAction;

/**
 * 网页
 */
class IndexAction extends BaseAction {
	public function run() {
		$typeId = ServerType::findTypeIdWithCode("gateway");
		if ($typeId == 0) {
			return;
		}
		$servers = Server::findAllServersWithType($typeId);
		if (!empty($servers)) {
			g("@.server.index", [
				"serverId" => $servers[0]->id
			]);
		}
	}
}

?>