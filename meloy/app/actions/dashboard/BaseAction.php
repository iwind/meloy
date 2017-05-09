<?php

namespace app\actions\dashboard;

use app\classes\AuthAction;

class BaseAction extends AuthAction {
	public function before() {
		parent::before();

		$this->data->menu = "dashboard";
	}
}

?>