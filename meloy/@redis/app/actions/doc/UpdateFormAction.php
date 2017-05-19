<?php

namespace redis\app\actions\doc;

use tea\page\SemanticPage;

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
		$this->data->value = $this->_redis()->get($key);

		$this->view("updateFormString");
	}

	private function _runHash($key) {
		$this->data->count = $this->_redis()->hLen($key);
		$this->data->value = $this->_redis()->hGetAll($key);

		$this->view("updateFormHash");
	}

	private function _runSet($key) {
		$this->data->count = $this->_redis()->sCard($key);
		$this->data->items = $this->_redis()->sGetMembers($key);

		$this->view("updateFormSet");
	}

	private function _runZset($key) {
		$this->data->count = $this->_redis()->zSize($key);

		$page = new SemanticPage();
		$page->total($this->data->count);
		$page->size(30);
		$page->autoQuery();
		$this->data->page = $page->asHtml();
		$this->data->offset = $page->offset();

		$items = $this->_redis()->zRange($key, $page->offset(), $page->offset() + $page->size() - 1);
		$this->data->items = [];
		foreach ($items as $item) {
			$this->data->items[] = (object)[
				"value" => $item,
				"score" => $this->_redis()->zScore($key, $item)
			];
 		}

		$this->view("updateFormZset");
	}

	private function _runList($key) {
		$this->data->count = $this->_redis()->lSize($key);

		$page = new SemanticPage();
		$page->total($this->data->count);
		$page->size(10);
		$page->autoQuery();
		$this->data->page = $page->asHtml();
		$this->data->offset = $page->offset();
		$this->data->items = $this->_redis()->lGetRange($key, $page->offset(), $page->offset() + $page->size() - 1);

		$this->view("updateFormList");
	}
}

?>