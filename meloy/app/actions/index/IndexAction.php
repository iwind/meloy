<?php

namespace app\actions\index;

use tea\Action;
use tea\auth\ShouldAuth;
use tea\Tea;

class IndexAction extends Action {
	public function run(ShouldAuth $userAuth) {
		//判断是否已经安装
		$db = o("db");
		if (is_null($db)) {
			g("install");
		}

		if (preg_match("/%\\{dbname\\}/", $db["dbs"]["default"]["dsn"])) {
			g("install");
		}

		//判断是否登录
		if ($userAuth->validate()) {
			g("dashboard");
		}

		if (Tea::shared()->host() == "demo.meloy.cn") {
			$this->view("indexDemo");
		}
	}
}

?>