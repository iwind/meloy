<?php

namespace app\actions\settings;

use app\models\user\User;

class ProfileAction extends BaseAction {
	protected $_subMenu = "profile";

	public function run() {
		$user = User::find($this->userId());
		$this->data->user = [
			"nickname" => $user->nickname,
			"email" => $user->email
		];
	}
}

?>