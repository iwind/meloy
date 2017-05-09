<?php

namespace app\actions\install;

use app\models\user\User;

class FinishAction extends BaseAction {
	public function run() {
		$this->data->user = User::find(1);
	}
}

?>