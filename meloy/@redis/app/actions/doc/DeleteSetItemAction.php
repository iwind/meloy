<?php

namespace redis\app\actions\doc;

class DeleteSetItemAction extends BaseAction {
	public function run(string $key, string $item) {
		$this->_redis()->sRemove($key, $item);
	}
}

?>