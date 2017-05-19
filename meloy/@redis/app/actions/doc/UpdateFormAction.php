<?php

namespace redis\app\actions\doc;

class UpdateFormAction extends BaseAction {
	public function run(string $key, string $g) {
		$this->data->key = $key;
		$this->data->g = $g;

		$type = $this->_redis()->type($key);
		if ($type == \Redis::REDIS_NOT_FOUND) {
			$this->_runNotFound();
		}
		else if ($type == \Redis::REDIS_STRING) {
			$this->_runString($key);
		}
		else if ($type == \Redis::REDIS_SET) {
			$this->_runSet($key);
		}
		else if ($type == \Redis::REDIS_LIST) {
			$this->_runList($key);
		}
		else if ($type == \Redis::REDIS_ZSET) {
			$this->_runZset($key);
		}
		else if ($type == \Redis::REDIS_HASH) {
			$this->_runHash($key);
		}
	}

	private function _runNotFound() {
		$this->view("updateFormNotFound");
	}

	private function _runString($key) {

		$this->view("updateFormString");
	}

	private function _runHash($key) {
		$this->data->count = $this->_redis()->hLen($key);
		$this->data->value = $this->_redis()->hGetAll($key);

		$this->view("updateFormHash");
	}

	private function _runSet($key) {
		$this->view("updateFormSet");
	}

	private function _runZset($key) {
		$this->view("updateFormZset");
	}

	private function _runList($key) {
		$this->view("updateFormList");
	}
}

?>