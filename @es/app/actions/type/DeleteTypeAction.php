<?php

namespace es\app\actions\type;

use es\Exception;

class DeleteTypeAction extends BaseAction {
	public function run() {
		//执行删除
		if (!is_empty($this->_type)) {
			try {
				$this->_api->delete("/" . $this->_index . "/_mapping" . $this->_type, "");
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