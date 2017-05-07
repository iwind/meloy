<?php

namespace app\actions\index;

use tea\Action;
use tea\auth\ShouldAuth;
use tea\Exception;

class IndexAction extends Action {
	public function run(ShouldAuth $userAuth) {
		//判断是否已经安装
		$db = o("db");
		if (is_null($db)) {
			throw new Exception("can not find database config file at 'app/configs/db.php'");
		}

		if (preg_match("/%\\{dbname\\}/", $db["dbs"]["default"]["dsn"])) {
			g("install");
		}

		//判断是否登录
		if ($userAuth->validate()) {
			g("dashboard");
		}
	}
}

?>