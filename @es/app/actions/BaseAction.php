<?php

namespace es\app\actions;

use app\classes\AuthAction;

class BaseAction extends AuthAction {
	public function before() {
		parent::before();

		$this->data->menu = "@es";
		$this->data->subMenus = [
			[
				"name" => "主机管理"
			]
		];
	}
}

?>