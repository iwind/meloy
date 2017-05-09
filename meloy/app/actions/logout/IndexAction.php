<?php

namespace app\actions\logout;

use tea\Action;
use tea\auth\ShouldAuth;

class IndexAction extends Action {
	public function run(ShouldAuth $userAuth) {
		//注销SESSION
		$userAuth->unset();

		//跳转到首页
		g("index");
	}
}

?>