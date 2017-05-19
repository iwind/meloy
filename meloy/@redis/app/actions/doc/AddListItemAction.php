<?php

namespace redis\app\actions\doc;

class AddListItemAction extends BaseAction {
	public function run(string $key, int $position, int $index, string $pivot, string $value) {
		if ($position == "1") {// 最后一个
			$this->_redis()->rPushx($key, $value);
		}
		else if ($position == "2") {// 第一个
			$this->_redis()->lPushx($key, $value);
		}
		else if ($position == "3") {// 插入
			//是否超出范围
			$pivotValue = $this->_redis()->lGet($key, $index);
			if ($pivotValue === false) {
				$this->field("index", "要插入的位置超出范围")->fail();
			}

			$position = "after";
			if ($pivot == "before") {
				$position = \Redis::BEFORE;
			}
			else if ($pivot == "after") {
				$position = \Redis::AFTER;
			}

			$pivot = nil . "__PIVOT__" . uniqid();
			$this->_redis()->lSet($key, $index, $pivot);
			$this->_redis()->lInsert($key, $position, $pivot, $value);

			if ($position == \Redis::BEFORE) {
				if ($index >= 0) {
					$this->_redis()->lSet($key, $index + 1, $pivotValue);
				}
				else {
					$this->_redis()->lSet($key, $index, $pivotValue);
				}
			}
			else {
				if ($index >= 0) {
					$this->_redis()->lSet($key, $index, $pivotValue);
				}
				else {
					$this->_redis()->lSet($key, $index - 1, $pivotValue);
				}
			}
		}
		else {// 默认最后一个
			$this->_redis()->rPushx($key, $value);
		}

		$this->refresh()->success("保存成功");
	}
}

?>