<?php

namespace app\actions\index;

use tea\Action;
use tea\auth\ShouldAuth;

class IndexAction extends Action {
	public function run(ShouldAuth $userAuth) {
		if ($userAuth->validate()) {
			g("dashboard");
		}
	}
}

?>