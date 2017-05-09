<?php

namespace app\actions\users;

use app\classes\AuthAction;

class BaseAction extends AuthAction {
	public function before() {
		parent::before();

		$this->data->menu = "users";
	}
}

?>