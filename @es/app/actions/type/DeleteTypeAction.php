<?php

namespace es\app\actions\type;

use es\Exception;

class DeleteTypeAction extends BaseAction {
	public function run() {
		//执行删除
		if (!is_empty($this->_type)) {
			try {

			} catch (Exception $e) {
				$this->fail($e->getMessage());
			}
		}

		//跳转到主机
		$this->next("@.server", [
			"serverId" => $this->_server->id,
			"index" => $this->_index
		])->success("删除成功");
	}
}

?>