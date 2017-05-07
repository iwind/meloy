<?php

namespace app\actions\install;

use app\models\user\User;
use tea\Action;

class FinishAction extends Action {
	public function run() {
		$this->data->user = User::find(1);
	}
}

?>