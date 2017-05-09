<?php

namespace app\jobs;

use app\models\user\User;
use tea\Job;

class InitJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return "app.init";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		//生成用户
		User::genFirstUser();

		$this->println(json_encode(User::query()->findAll(), JSON_PRETTY_PRINT));
	}
}

?>