<?php

namespace redis\app\actions\doc;

class DeleteAction extends BaseAction {
	public function run(string $key) {
		if (is_empty($key)) {
			$this->fail("请输入正确的键");
		}

		$this->_redis()->delete($key);
	}
}

?>