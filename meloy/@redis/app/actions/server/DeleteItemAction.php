<?php

namespace redis\app\actions\server;

class DeleteItemAction extends BaseAction {
	public function run(string $key) {
		if (is_empty($key)) {
			$this->fail("请输入正确的键");
		}

		$this->_redis()->delete($key);
	}
}

?>