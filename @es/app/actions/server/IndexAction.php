<?php

namespace es\app\actions\server;

use app\models\server\Server;
use es\app\actions\BaseAction;

class IndexAction extends BaseAction {
	public function run(int $serverId) {
		//检查主机
		$server = Server::find($serverId);
		if (!$server) {
			return 404;
		}

		//查询索引

	}
}

?>