<?php

namespace es\app\actions\type;

class DeleteTypeFormAction extends BaseAction {
	public function run() {
		//是否支持删除
		$this->data->supportsDelete = false;
	}
}

?>