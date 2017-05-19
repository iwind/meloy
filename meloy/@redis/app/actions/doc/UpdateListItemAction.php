<?php

namespace redis\app\actions\doc;

class UpdateListItemAction extends BaseAction {
	public function run(string $key, int $index, string $value) {
		$this->_redis()->lSet($key, $index, $value);
	}
}

?>