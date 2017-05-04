<?php

namespace es\app\actions\indice;

class DeleteIndexAction extends BaseAction {
	public function run() {
		if (!is_empty($this->_index)) {
			$this->_api->delete("/" . $this->_index, "");
		}

		//跳转到主机
		$this->next("@.server", [ "serverId" => $this->_server->id ]);
	}
}

?>