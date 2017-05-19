<?php

namespace redis\app\actions\doc;

class DeleteListItemAction extends BaseAction {
	public function run(string $key, int $index) {
		$tmp = nil . "_TO_BE_DELETED";
		$this->_redis()->lSet($key, $index, $tmp);
		$this->_redis()->lRemove($key, $tmp, 1);
	}
}

?>