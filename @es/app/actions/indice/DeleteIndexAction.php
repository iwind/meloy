<?php

namespace es\app\actions\indice;

use es\api\DeleteIndexApi;
use es\Exception;

class DeleteIndexAction extends BaseAction {
	public function run() {
		//执行删除
		if (!is_empty($this->_index)) {
			/**
			 * @var DeleteIndexApi $api
			 */
			$api = $this->_server->api(DeleteIndexApi::class);
			$api->index($this->_index);
			try {
				$api->delete();
			} catch (Exception $e) {
				$this->fail($e->getMessage());
			}
		}

		//跳转到主机
		$this->next("@.server", [ "serverId" => $this->_server->id ])
			->success("删除成功");
	}
}

?>