<?php

namespace es\app\actions\type;

use app\models\server\Server;
use es\api\CountApi;
use es\api\GetIndexApi;
use es\Exception;
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
		$request = Request::shared();
		$index = $request->param("index");
		$this->_index = $index;
		$this->data->index = (object)[
			"name" => $index
		];

		$type = $request->param("type");
		$this->_type = $type;
		$this->data->type = (object)[
			"name" => $type
		];

		if ($request->isGet()) {
			//数量
			$countApi = $this->_server->api(CountApi::class);
			/** @var CountApi $countApi */
			$countApi->index($this->_index)
				->type($this->_type);
			$count = null;
			try {
				$count = $countApi->count();
			} catch (Exception $e) {
			}

			$this->data->tabbar = [
				[
					"name" => "类型(" . $type . ")",
					"subName" => is_null($count) ? "" : "共{$count}个文档",
					"url" => u(".index", [
						"serverId" => $serverId,
						"index" => $index,
						"type" => $type
					]),
					"active" => $this->name() == "index",
				],
				[
					"name" => "查询构造器",
					"url" => u(".builder", [
						"serverId" => $serverId,
						"index" => $index,
						"type" => $type
					]),
					"active" => $this->name() == "builder"
				],
				[
					"name" => "添加数据",
					"url" => u(".addDocForm", [
						"serverId" => $serverId,
						"index" => $index,
						"type" => $type
					]),
					"active" => $this->name() == "addDocForm"
				],
				[
					"name" => "字段",
					"url" => u(".fields", [
						"serverId" => $serverId,
						"index" => $index,
						"type" => $type
					]),
					"active" => $this->name() == "fields"
				],
				[
					"name" => "删除",
					"url" => u(".deleteForm", [
						"serverId" => $serverId,
						"index" => $index,
						"type" => $type
					]),
					"active" => $this->name() == "deleteForm"
				]
			];
		}
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