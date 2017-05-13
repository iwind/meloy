<?php

namespace redis\app\actions\server;

use app\models\server\Server;
use tea\Request;

class BaseAction extends \redis\app\actions\BaseAction {
	/**
	 * 主机对象
	 *
	 * @var Server
	 */
	protected $_server;

	public function before() {
		parent::before();

		//主机ID
		$serverId = Request::shared()->param("serverId");
		$this->data->serverId = $serverId;

		//检查主机
		$server = Server::find($serverId);
		if (!$server) {
			return 404;
		}

		//主机信息
		$this->_server = $server;
		$this->data->server = (object)[
			"id" => $server->id,
			"name" => $server->name,
			"host" => $server->host,
			"port" => $server->port
		];

		//菜单
		$this->data->tabbar = [
			[
				"name" => $server->name . "(" . $server->host . ":" . $server->port . ")",
				"url" => u(".index", [ "serverId" => $serverId ]),
				"active" => $this->name() == "index"
			],
			[
				"name" => "修改",
				"url" => u(".updateForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "updateForm"
			],
			[
				"name" => "删除",
				"url" => u(".deleteForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "deleteForm"
			]
		];
	}
}

?>