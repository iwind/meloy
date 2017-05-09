<?php

namespace app\actions\install;

use tea\Action;
use tea\auth\ShouldAuth;

class BaseAction extends Action {
	/**
	 * 身份认证
	 *
	 * @var ShouldAuth
	 */
	private $_auth;

	public function before() {
		parent::before();

		$this->_auth = ShouldAuth::newForParam("install");

		//检查是否需要安装
		$db = o("db");

		if (isset($db["dbs"]["default"]["dsn"])
			&& !preg_match("/%\\{dbname\\}/", $db["dbs"]["default"]["dsn"])) {
			if (!$this->_auth->validate()) {
				g("");
			}
		}
	}

	/**
	 * 身份认证
	 *
	 * @return ShouldAuth
	 */
	public function auth() {
		return $this->_auth;
	}
}

?>