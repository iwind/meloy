<?php

namespace app\actions\settings;

use app\models\user\User;

class ProfileAction extends BaseAction {
	protected $_subMenu = "profile";

	public function run() {
		$this->data->user = User::find($this->userId());
	}
}

?>