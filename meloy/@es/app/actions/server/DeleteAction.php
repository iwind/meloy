<?php

namespace es\app\actions\server;

use app\models\server\Server;

class DeleteAction extends BaseAction {
	public function run() {
		Server::disableServer($this->_server->id);

		$this->next("@")->success("删除成功");
	}
}

?>