<?php

namespace es\app\actions\type;

use app\models\server\Server;
use es\API;
use es\api\GetIndexApi;
use tea\Arrays;
use tea\Request;

class BaseAction extends \es\app\actions\BaseAction {
	/**
	 * 主机对象
	 *
	 * @var Server
	 */
	protected $_server;

	/**
	 * 索引名
	 *
	 * @var string
	 */
	protected $_index;

	/**
	 * 类型
	 *
	 * @var string
	 */
	protected $_type;

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
		$index = Request::shared()->param("index");
		$this->_index = $index;
		$this->data->index = (object)[
			"name" => $index
		];

		$type = Request::shared()->param("type");
		$this->_type = $type;
		$this->data->type = (object)[
			"name" => $type
		];
		$this->data->tabbar = [
			[
				"name" => "类型(" . $type . ")",
				"url" => u(".index", [
					"serverId" => $serverId,
					"index" => $index,
					"type" => $type
				]),
				"active" => $this->name() == "index"
			],
			[
				"name" => "删除",
				"url" => u(".deleteTypeForm", [
					"serverId" => $serverId,
					"index" => $index,
					"type" => $type
				]),
				"active" => $this->name() == "deleteTypeForm"
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