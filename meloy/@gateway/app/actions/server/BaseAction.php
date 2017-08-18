<?php

namespace gateway\app\actions\server;

use app\models\server\Server;
use tea\Exception;
use tea\Request;

class BaseAction extends \gateway\app\actions\BaseAction {
	protected $_serverId = 0;

	/**
	 * @var Server
	 */
	protected $_server;

	public function before() {
		parent::before();

		$this->_serverId = Request::shared()->param("serverId");
		$server = Server::find($this->_serverId);
		if (!$server) {
			throw new Exception("Can not find server with id '{$this->_serverId}'");
		}

		$this->_server = $server;

		$this->data->server =  (object)[
			"id" => $server->id,
			"name" => $server->name,
			"host" => $server->host,
			"port" => $server->port,
			"options" => json_decode($server->options)
		];

		$this->data->tabbar = [
			[
				"name" => "API列表",
				"url" => u("@.server", [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("index")
			],
			[
				"name" => "日志",
				"url" => u("@.server.watch",  [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("watch")
			],
			[
				"name" => "排行",
				"url" => u("@.server.ranks",  [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("ranks")
			],
			[
				"name" => "监控",
				"url" => u("@.server.monitor",  [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("monitor")
			],
			[
				"name" => "统计",
				"url" => u("@.server.stat",  [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("stat")
			],
			[
				"name" => "应用信息",
				"url" => u("@.server.updateForm", [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("updateForm")
			],
			[
				"name" => "删除应用",
				"url" => u("@.server.deleteForm", [ "serverId" => $this->_serverId ]),
				"active" => $this->hasName("deleteForm")
			]
		];
	}
}

?>