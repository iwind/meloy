<?php

namespace redis\app\actions\doc;

use tea\Must;

class AddSetItemAction extends BaseAction {
	public function run(string $key, string $value, Must $must) {
		$must->field("value", $value)
			->require("请输入元素值");

		$this->_redis()->sAdd($key, $value);

		$this->refresh()->success("保存成功");
	}
}

?>