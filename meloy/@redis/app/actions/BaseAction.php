<?php

namespace redis\app\actions;

use app\classes\AuthAction;
use app\models\server\Server;
use app\models\server\ServerType;
use redis\Exception;
use tea\Request;

class BaseAction extends AuthAction {
	protected $_subMenu;

	/**
	 * 主机信息
	 *
	 * @var Server
	 */
	private $_server;

	private $_redis;

	public function before() {
		parent::before();

		//加载Redis操作库
		import(TEA_ROOT . DS . "@redis/app/libs");

		//检查Redis是否已安装扩展
		if (!class_exists("\\Redis", false)) {
			throw new Exception("要想正常连接到Redis，请先安装php_redis扩展：<a href=\"https://github.com/phpredis/phpredis/\" target='_blank'>https://github.com/phpredis/phpredis/</a>");
		}

		$this->data->menu = "@redis";

		//用户创建的主机
		$request = Request::shared();
		$serverId = $request->param("serverId");
		$subMenus = [];
		$serverTypeId = ServerType::findTypeIdWithCode("redis");
		foreach (Server::findUserServersWithType($this->userId(), $serverTypeId) as $server) {
			$menu = [
				"name" => $server->name . "(" . $server->host . ":" . $server->port . ")",
				"url" => u("@.server", [ "serverId" => $server->id ]),
				"active" => $server->id == $serverId,
				"items" => []
			];

			//主机
			if ($server->id == $serverId) {
				$this->_server = $server;
			}

			$subMenus[] = $menu;
		}

		//获取用户可以使用的主机

		//定义菜单
		$subMenus[] = [
			"name" => "[添加新主机]",
			"url" => u("@.server.addForm"),
			"active" => $this->_subMenu == "addServer"
		];
		$this->data->subMenus = [
			[
				"name" => "主机管理",
				"items" => $subMenus
			],
		];
	}

	/**
	 * 获取Redis对象
	 *
	 * @return \Redis
	 * @throws
	 */
	public function _redis() {
		if (!$this->_redis) {
			$this->_redis = new \Redis();
			if (!$this->_redis->connect($this->_server->host, $this->_server->port)) {
				throw new Exception("无法连接到Redis：{$this->_server->host}:{$this->_server->port}");
			}

			$options = json_decode($this->_server->options);
			if (is_object($options) && isset($options->password) && !is_empty($options->password)) {
				if (!$this->_redis->auth($options->password)) {
					throw new Exception("已连接到Redis：{$this->_server->host}:{$this->_server->port}，但密码校验失败");
				}
			}
		}

		return $this->_redis;
	}
}

?>