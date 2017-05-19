<?php

namespace redis\app\actions\doc;

class UpdateZsetItemAction extends BaseAction {
	public function run(string $key, string $item, string $value, float $score) {
		$this->_redis()->zRem($key, $item);
		$this->_redis()->zAdd($key, $score, $value);
	}
}

?>