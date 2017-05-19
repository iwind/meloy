<?php

namespace redis\app\actions\doc;

class DeleteZsetItemAction extends BaseAction {
	public function run(string $key, string $item) {
		$this->_redis()->zDelete($key, $item);
	}
}

?>