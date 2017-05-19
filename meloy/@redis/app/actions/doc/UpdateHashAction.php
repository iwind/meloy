<?php

namespace redis\app\actions\doc;

class UpdateHashAction extends BaseAction {
	public function run(string $key, array $itemKeys, array $itemValues) {
		$this->_redis()->delete($key);
		foreach ($itemKeys as $index => $itemKey) {
			if (isset($itemValues[$index])) {
				$this->_redis()->hSet($key, $itemKey, $itemValues[$index]);
			}
		}

		$this->refresh()->success("保存成功");
	}
}

?>