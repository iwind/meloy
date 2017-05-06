<?php

namespace es\app\actions;

use app\classes\AuthAction;
use app\models\server\Server;
use es\api\GetIndexApi;
use es\Exception;
use tea\Arrays;
use tea\Request;

class BaseAction extends AuthAction {
	protected $_subMenu;

	public function before() {
		parent::before();

		//加载ES操作库
		import(TEA_ROOT . DS . "@es/app/libs");

		$this->data->menu = "@es";

		//用户创建的主机
		$request = Request::shared();
		$serverId = $request->param("serverId");
		$index = $request->param("index");
		$type = $request->param("type");
		$subMenus = [];
		foreach (Server::findUserServersWithType($this->userId(), Server::TYPE_ES) as $server) {
			$menu = [
				"name" => $server->name . "(" . $server->host . ":" . $server->port . ")",
				"url" => u("@.server", [ "serverId" => $server->id ]),
				"active" => $server->id == $serverId,
				"items" => []
			];

			//索引
			if ($server->id == $serverId) {
				/**
				 * @var GetIndexApi $api
				 */
				$api = $server->api(GetIndexApi::class);

				$hasError = false;
				try {
					$indexes = $api->getAll();
				} catch (Exception $e) {
					$hasError = true;
					$indexes = [];
				}

				if ($hasError) {
					$menu["items"][] = [
						"name" => "[无法连接此主机]"
					];
				}
				else {
					$menu["items"][] = [
						"name" => "索引 &raquo;"
					];

					$indexItems = [];
					foreach ($indexes as $indexName => $info) {
						$subItems = [];

						$subItems[] = [
							"name" => "类型 &raquo;"
						];

						if ($index == $indexName) {
							$typeItems = [];
							foreach ($info->mappings as $typeName => $mapping) {
								$typeItems[] = [
									"name" => $typeName,
									"url" => u("@.type", [
										"serverId" => $serverId,
										"index" => $indexName,
										"type" => $typeName
									]),
									"active" => $serverId == $server->id && $index == $indexName && $type == $typeName
								];
							}

							$typeItems = Arrays::sort($typeItems, "name");
							foreach ($typeItems as $item) {
								$subItems[] = $item;
							}
						}

						//@TODO 显示 aliases
						$indexItems[] = [
							"name" => $indexName,
							"url" => u("@.indice", ["serverId" => $serverId, "index" => $indexName]),
							"items" => $subItems,
							"active" => $serverId == $server->id && $index == $indexName
						];
					}

					$indexItems = Arrays::sort($indexItems, "name");
					foreach ($indexItems as $item) {
						$menu["items"][] = $item;
					}
				}
			}

			$subMenus[] = $menu;
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
	}
}

?>