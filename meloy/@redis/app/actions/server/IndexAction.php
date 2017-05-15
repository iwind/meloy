<?php

namespace redis\app\actions\server;

class IndexAction extends BaseAction {
	public function run(int $offset, string $q) {
		$offset = ($offset == 0) ? null : $offset;

		$this->data->isFirst = ($offset == null || $offset <= 0);

		$q = is_empty($q) ? null : $q;
		$keys = $this->_redis()->scan($offset, $q, 10);

		$items = [];

		if (empty($keys) && $this->data->isFirst) {
			$keys = $this->_redis()->keys($q);
			$offset = 0;
		}
		foreach ($keys as $key) {
			$value = $this->_redis()->get($key);
			$type = $this->_redis()->type($key);
			$typeName = "string";
			$count = 0;
			if ($type == \Redis::REDIS_STRING) {
				$typeName = "string";
			}
			else if ($type == \Redis::REDIS_HASH) {
				$typeName = "hash";
				$value = json_encode($this->_redis()->hGetAll($key), JSON_PRETTY_PRINT);
			}
			else if ($type == \Redis::REDIS_LIST) {
				$typeName = "list";

				$value = $this->_redis()->lGetRange($key, 0, 9);

				$count = $this->_redis()->lLen($key);
				if ($count > count($value)) {
					$value[] = "...";
				}

				$value = json_encode($value, JSON_PRETTY_PRINT);
			}
			else if ($type == \Redis::REDIS_SET) {
				$typeName = "set";
				$value = json_encode($this->_redis()->sGetMembers($key), JSON_PRETTY_PRINT);
			}
			else if ($type == \Redis::REDIS_ZSET) {
				$typeName = "zset";
				$value = $this->_redis()->zRange($key, 0, 9);

				$count = $this->_redis()->zSize($key);
				if ($count > count($value)) {
					$value[] = "...";
				}
				$value = json_encode($value, JSON_PRETTY_PRINT);
			}
			else if ($type == \Redis::REDIS_NOT_FOUND) {
				$typeName = "string";
			}

			$items[] = (object)[
				"key" => $key,
				"value" => $value,
				"type" => $typeName,
				"count" => $count
			];
		}

		$this->data->offset = $offset;
		$this->data->items = $items;
	}
}

?>