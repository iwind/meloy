<?php

namespace app\actions\settings;

use app\models\user\User;

class EmailAction extends BaseAction {
	protected $_subMenu = "email";

	public function run() {
		$user = User::find($this->userId());
		$this->data->user = [
			"nickname" => $user->nickname,
			"email" => $user->email
		];
	}
}

?>