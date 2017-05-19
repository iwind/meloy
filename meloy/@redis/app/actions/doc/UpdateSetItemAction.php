<?php

namespace redis\app\actions\doc;

use tea\Must;

class UpdateSetItemAction extends BaseAction {
	public function run(string $key, string $item, string $value, Must $must) {
		$must->field("value", $value)
			->require("请输入元素值");

		$this->_redis()->sRemove($key, $item);
		$this->_redis()->sAdd($key, $value);
	}
}

?>