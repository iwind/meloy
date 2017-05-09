<?php

namespace app\actions\install;

use tea\Action;

class BaseAction extends Action {
	public function before() {
		parent::before();

		//检查是否需要安装
		$db = o("db");

		if (isset($db["dbs"]["default"]["dsn"]) && !preg_match("/%\\{dbname\\}/", $db["dbs"]["default"]["dsn"])) {
			g("");
		}
	}
}

?>