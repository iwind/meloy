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
		$count = 0;
		foreach ($keys as $key) {
			$value = $this->_redis()->get($key);
			$type = "string";
			if ($value === false) {
				//是否为list
				$value = $this->_redis()->lGet($key, 0);
				if ($value !== false) {
					$type = "list";
					$value = $this->_redis()->lGetRange($key, 0, 9);

					$count = $this->_redis()->lLen($key);
					if ($count > count($value)) {
						$value[] = "...";
					}
				}

				//是否为hash
				if ($value === false) {
					$value = $this->_redis()->hGetAll($key);
					if (!empty($value)) {
						$value = json_unicode_to_utf8(json_encode($value, JSON_PRETTY_PRINT));
						$type = "hash";
					}
				}
			}
			$items[] = (object)[
				"key" => $key,
				"value" => $value,
				"type" => $type,
				"count" => $count
			];
		}

		$this->data->offset = $offset;
		$this->data->items = $items;
	}
}

?>