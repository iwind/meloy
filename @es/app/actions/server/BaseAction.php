<?php

namespace es\app\actions\server;

use app\models\server\Server;
use tea\Request;

class BaseAction extends \es\app\actions\BaseAction {
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
				"name" => "索引",
				"url" => u(".indexes", [ "serverId" => $serverId ]),
				"active" => $this->name() == "indexes"
			],
			[
				"name" => "创建索引",
				"url" => u(".createIndexForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "createIndexForm"
			],
			[
				"name" => "删除",
				"url" => u(".deleteServerForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "deleteServerForm"
			]
		];
	}
}

?>