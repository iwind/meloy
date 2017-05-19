<?php

namespace redis\app\actions\doc;

use tea\Must;

class AddZsetItemAction extends BaseAction {
	public function run(string $key, string $value, float $score, Must $must) {
		$must->field("value", $value)
			->require("请输入元素值");

		$this->_redis()->zAdd($key, $score, $value);

		$this->refresh()->success("保存成功");
	}
}

?>