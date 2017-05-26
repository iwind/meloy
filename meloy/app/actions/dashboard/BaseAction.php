<?php

namespace app\actions\dashboard;

use app\classes\AuthAction;

class BaseAction extends AuthAction {
	public function before() {
		parent::before();

		$this->data->menu = "dashboard";

		$this->data->tabbar = [
			[
				"name" => "可管理的主机",
				"url" => u("dashboard"),
				"active" => $this->name() == "index"
			],
			[
				"name" => "已安装插件",
				"url" => u("dashboard.modules"),
				"active" => $this->name() == "modules"
			],
			/**[
				"name" => "已安装小助手"
			],
			[
				"name" => "插件广场"
			]**/
		];
	}
}

?>