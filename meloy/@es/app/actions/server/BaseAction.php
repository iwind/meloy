<?php

namespace es\app\actions\server;

use app\models\server\Server;
use es\api\GetIndexApi;
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
				"name" => "修改",
				"url" => u(".updateForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "updateForm"
			],
			[
				"name" => "集群",
				"url" => u(".cluster", [ "serverId" => $serverId ]),
				"active" => $this->name() == "cluster"
			],
			[
				"name" => "监控",
				"url" => u(".monitor", [ "serverId" => $serverId ]),
				"active" => $this->name() == "monitor"
			],
			[
				"name" => "插件",
				"url" => u(".plugins", [ "serverId" => $serverId ]),
				"active" => $this->name() == "plugins"
			],
			[
				"name" => "删除",
				"url" => u(".deleteForm", [ "serverId" => $serverId ]),
				"active" => $this->name() == "deleteForm"
			]
		];
	}

	/**
	 * 取得ES服务版本
	 *
	 * @return string
	 */
	public function serverVersion() {
		/**
		 * @var GetIndexApi $api
		 */
		$api = $this->_server->api(GetIndexApi::class);
		$data = $api->get();
		return $data->version->number;
	}
}

?>