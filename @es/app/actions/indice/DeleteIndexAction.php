<?php

namespace es\app\actions\indice;

use es\Exception;

class DeleteIndexAction extends BaseAction {
	public function run() {
		//执行删除
		if (!is_empty($this->_index)) {
			try {
				$this->_api->delete("/" . $this->_index, "");
			} catch (Exception $e) {
				$this->fail($e->getMessage());
			}
		}

		//跳转到主机
		$this->next("@.server", [ "serverId" => $this->_server->id ]);
	}
}

?>