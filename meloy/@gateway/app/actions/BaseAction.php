<?php

namespace gateway\app\actions;

use app\classes\AuthAction;
use app\models\server\Server;
use app\models\server\ServerType;
use tea\Request;

class BaseAction extends AuthAction {
	protected $_subMenu = "";

	public function before() {
		parent::before();

		$request = Request::shared();

		if (Request::shared()->isGet()) {
			$serverId = $request->param("serverId");

			//取得所有应用
			$typeId = ServerType::findTypeIdWithCode("gateway");
			if ($typeId == 0) {
				$typeId = ServerType::createServerType("网关", "gateway");
			}
			$servers = Server::findAllServersWithType($typeId);
			$subMenus = [];
			foreach ($servers as $server) {
				$subMenus[] = [
					"name" => $server->name,
					"url" => u("@.server", [ "serverId" => $server->id ]),
					"active" => $serverId == $server->id
				];
			}

			//定义菜单
			$subMenus[] = [
				"name" => "[添加新应用]",
				"url" => u("@.server.addForm"),
				"active" => $this->_subMenu == "addServer"
			];

			$this->data->subMenus = [
				[
					"name" => "应用",
					"items" => $subMenus
				],
			];
		}
	}
}

?>