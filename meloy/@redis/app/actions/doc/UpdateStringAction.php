<?php

namespace redis\app\actions\doc;

class UpdateStringAction extends BaseAction {
	public function run(string $key, string $value) {
		$this->_redis()->set($key, $value);

		$this->refresh()->success("保存成功");
	}
}

?>