<?php

namespace es\app\actions\server;

use app\models\server\Server;

class DeleteServerAction extends BaseAction {
	public function run() {
		Server::disableServer($this->_server->id);

		$this->next("@")->success("删除成功");
	}
}

?>