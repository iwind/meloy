<?php

namespace redis\app\actions\doc;

use redis\Exception;

class RenameAction extends BaseAction {
	public function run(string $key, string $newKey, string $g) {
		try {
			$this->_redis()->renameKey($key, $newKey);
		} catch (Exception $e) {
			$this->field("newKey", $e->getMessage())->fail();
		}

		$this->next(".updateForm", [
			"key" => $newKey,
			"serverId" => $this->_server->id,
			"g" => $g
		]);
		$this->success("修改成功");
	}
}

?>