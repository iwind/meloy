<?php

namespace es\app\actions;

use app\classes\AuthAction;
use app\models\server\Server;
use tea\Request;

class BaseAction extends AuthAction {
	protected $_subMenu;

	public function before() {
		parent::before();

		$this->data->menu = "@es";

		//用户创建的主机
		$subMenus = [];
		foreach (Server::findUserServersWithType($this->userId(), Server::TYPE_ES) as $server) {
			$subMenus[] = [
				"name" => $server->name . "(" . $server->host . ":" . $server->port . ")",
				"url" => u("@.server", [ "serverId" => $server->id ]),
				"active" => $server->id == Request::shared()->param("serverId")
			];
		}

		//获取用户可以使用的主机


		//定义菜单
		$subMenus[] = [
			"name" => "[添加新主机]",
			"url" => u("@.server.add"),
			"active" => $this->_subMenu == "addServer"
		];
		$this->data->subMenus = [
			[
				"name" => "主机管理",
				"items" => $subMenus
			],
		];

		//加载ES操作库
		import(TEA_ROOT . DS . "@es/app/libs");
	}
}

?>