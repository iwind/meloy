<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;

/**
 * 成员添加表单
 */
class CreateFormAction extends BaseAction {
	public function run() {
		$this->validateAdmin();
	}
}

?>