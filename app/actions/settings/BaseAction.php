<?php

namespace app\actions\settings;

use app\classes\AuthAction;

class BaseAction extends AuthAction {
	protected $_subMenu;

	public function before() {
		parent::before();

		$this->data->menu = "settings";

		$this->data->subMenus = [
			[
				"name" => "设置",
				"url" => u(".index"),
				"active" => false,
				"items" => [
					[
						"name" => "个人资料",
						"active" => $this->_subMenu == "profile",
						"url" => u(".profile")
					],
					[
						"name" => "登录邮箱",
						"active" => $this->_subMenu == "email",
						"url" => u(".email")
					],
					[
						"name" => "登录密码",
						"active" => $this->_subMenu == "password",
						"url" => u(".password")
					]
				]
			]
		];
	}
}

?>