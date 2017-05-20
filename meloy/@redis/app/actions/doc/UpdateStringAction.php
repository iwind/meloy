<?php

namespace redis\app\actions\doc;

class UpdateStringAction extends BaseAction {
	public function run(string $key, string $value) {
		$ttl = $this->_redis()->ttl($key);

		if ($ttl === false) {
			$ttl = -1;
		}

		$this->_redis()->set($key, $value);

		if ($ttl >= 0) {
			$this->_redis()->expire($key, $ttl);
		}

		$this->refresh()->success("保存成功");
	}
}

?>