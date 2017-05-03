<?php

namespace app\actions\settings;

use app\models\user\User;

class PasswordAction extends BaseAction {
	protected $_subMenu = "password";

	public function run() {
		$this->data->user = User::find($this->userId());
	}
}

?>