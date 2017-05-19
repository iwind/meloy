<?php

namespace redis\app\actions\doc;

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
		$g = Request::shared()->param("g");
		$this->data->tabbar = [
			[
				"name" => $server->name . "(" . $server->host . ":" . $server->port . ")",
				"url" =>  $g ?? u("@.server", [ "serverId" => $serverId ]),
				"active" => $this->name() == "index"
			],
			[
				"name" => "编辑数据",
				"url" => u(".updateForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "updateForm"
			],
		];
	}
}

?>