<?php

namespace app\actions\team;

class CreateFormAction extends BaseAction {
	public function run() {
		if ($this->_team != null) {
			g(".index");
		}
	}
}

?>