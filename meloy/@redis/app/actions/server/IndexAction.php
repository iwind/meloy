<?php

namespace redis\app\actions\server;

use app\classes\DateHelper;
use redis\Exception;

class IndexAction extends BaseAction {
	public function run(int $offset, bool $scan = true, string $q, DateHelper $dateHelper) {
		//是否能连接
		try {
			$this->_redis();
		} catch (Exception $e) {
			$this->data->error = $e->getMessage();
			return;
		}

		$offset = ($offset == 0) ? null : $offset;

		$this->data->isFirst = ($offset == null || $offset <= 0);

		$q = is_empty($q) ? null : $q;
		$keys = $scan ? $this->_redis()->scan($offset, $q, 10) : [];

		$docs = [];

		if (empty($keys)) {
			$scan = false;

			if ($this->data->isFirst) {
				$offset = 0;
			}

			$keys = $this->_redis()->keys($q);
			$count = count($keys);
			$keys = array_slice($keys, $offset ?? 0, 10);

			if ($count > 10) {
				$offset += 10;
			}
		}
		$this->data->scan = $scan;

		foreach ($keys as $key) {
			$value = $this->_redis()->get($key);
			$type = $this->_redis()->type($key);
			$typeName = "string";
			$count = 0;

			$realValue = null;
			$realType = null;
			$realTypeName = null;

			//超时时间
			$ttl = $this->_redis()->ttl($key);

			if ($type == \Redis::REDIS_STRING) {
				$typeName = "string";

				//尝试解析为PHP
				if (preg_match("/^(a|s|O|i|d|b):\\d+/", $value)) {
					$phpValue = @unserialize($value);
					if ($phpValue !== false) {
						$realValue = var_export($phpValue, true);
						$realType = "php serializer";
						$realTypeName = "PHP序列化";
					}
				}

				//尝试解析为JSON
				if (is_null($realValue) && preg_match("/(^\\[.*\\]$)|(^\\{.*\\}$)/s", $value)) {
					$jsonValue = json_decode($value);
					if (is_object($jsonValue) || is_array($jsonValue)) {
						$realValue = json_encode($jsonValue, JSON_PRETTY_PRINT);
						$realType = "json";
						$realTypeName = "JSON";
					}
				}

				//尝试解析为XML
				if (is_null($realValue) && preg_match("/<(\\w+)([^>]*)>.+<\\/\\1>/", $value)) {
					if (@simplexml_load_string($value)) {
						$realValue = $value;
						$realType = "xml";
						$realTypeName = "XML";
					}
				}

				//尝试解析为时间戳
				if (is_null($realValue) && preg_match("/^1\\d{9}$/", $value)) {
					$realValue = date("Y-m-d H:i:s", $value);
					$realType = "timestamp";
					$realTypeName = "时间戳";
				}

				//尝试解析为URL
				if (is_null($realValue) && preg_match("/^(ftp|http|https):\\/\\//", $value)) {
					$realValue = $value;
					$realType = "url";
					$realTypeName = "URL";
				}

				//尝试解析为E-MAIL

				//尝试解析为地址

				//尝试解析为经纬度
			}
			else if ($type == \Redis::REDIS_HASH) {
				$typeName = "hash";
				$value = json_unicode_to_utf8(json_encode($this->_redis()->hGetAll($key), JSON_PRETTY_PRINT));
				$count = $this->_redis()->hLen($key);
			}
			else if ($type == \Redis::REDIS_LIST) {
				$typeName = "list";

				$value = $this->_redis()->lGetRange($key, 0, 9);

				$count = $this->_redis()->lLen($key);
				if ($count > count($value)) {
					$value[] = "...";
				}

				$value = json_unicode_to_utf8(json_encode($value, JSON_PRETTY_PRINT));
			}
			else if ($type == \Redis::REDIS_SET) {
				$typeName = "set";
				$value = json_unicode_to_utf8(json_encode($this->_redis()->sGetMembers($key), JSON_PRETTY_PRINT));
				$count = $this->_redis()->sCard($key);
			}
			else if ($type == \Redis::REDIS_ZSET) {
				$typeName = "zset";
				$value = $this->_redis()->zRange($key, 0, 9);

				$count = $this->_redis()->zSize($key);
				if ($count > count($value)) {
					$value[] = "...";
				}
				$value = json_unicode_to_utf8(json_encode($value, JSON_PRETTY_PRINT));
			}
			else if ($type == \Redis::REDIS_NOT_FOUND) {
				$typeName = "string";
			}

			$docs[] = (object)[
				"key" => $key,
				"value" => $value,
				"type" => $typeName,
				"count" => $count,
				"realType" => $realType,
				"realTypeName" => $realTypeName,
				"realValue" => $realValue,
				"ttl" => $ttl,
				"ttlFormat" => $dateHelper->format($ttl),
			];
		}

		$this->data->offset = $offset;
		$this->data->docs = $docs;

		//是否有下一页
		$this->data->hasNext = $offset > 0 && count($this->_redis()->scan($offset, $q, 10)) > 0;
	}
}

?>