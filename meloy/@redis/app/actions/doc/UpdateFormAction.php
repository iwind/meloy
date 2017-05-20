<?php

namespace redis\app\actions\doc;

use app\classes\DateHelper;
use tea\page\SemanticPage;

class UpdateFormAction extends BaseAction {
	public function run(string $key, string $g, DateHelper $dateHelper) {
		$this->data->key = $key;
		$this->data->g = $g;

		$type = $this->_redis()->type($key);
		$typeName = "";
		if ($type == \Redis::REDIS_NOT_FOUND) {
			$this->_runNotFound();
		}
		else if ($type == \Redis::REDIS_STRING) {
			$typeName = "string";
			$this->_runString($key);
		}
		else if ($type == \Redis::REDIS_SET) {
			$typeName = "set";
			$this->_runSet($key);
		}
		else if ($type == \Redis::REDIS_LIST) {
			$typeName = "list";
			$this->_runList($key);
		}
		else if ($type == \Redis::REDIS_ZSET) {
			$typeName = "zset";
			$this->_runZset($key);
		}
		else if ($type == \Redis::REDIS_HASH) {
			$typeName = "hash";
			$this->_runHash($key);
		}

		$ttl = $this->_redis()->ttl($key);
		$this->data->doc = (object)[
			"type" => $typeName,
			"ttl" => $ttl,
			"ttlFormat" => $dateHelper->format($ttl)
		];
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