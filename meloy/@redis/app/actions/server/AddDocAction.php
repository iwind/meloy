<?php

namespace redis\app\actions\server;

use app\classes\DateHelper;
use tea\Must;

class AddDocAction extends BaseAction {
	public function run(string $key, string $type, int $ttl, int $timeCount, string $timeType, Must $must, DateHelper $dateHelper) {
		$must->field("key", $key)
			->require("请输入键(KEY)");

		$oldType = $this->_redis()->type($key);

		if ($type == "string") {
			if ($oldType != \Redis::REDIS_NOT_FOUND && $oldType != \Redis::REDIS_STRING) {
				$this->_redis()->delete($key);
			}

			if ($oldType != \Redis::REDIS_STRING) {
				$this->_redis()->set($key, "");
			}
		}
		else if ($type == "hash") {
			if ($oldType != \Redis::REDIS_NOT_FOUND && $oldType != \Redis::REDIS_HASH) {
				$this->_redis()->delete($key);
			}

			if ($oldType != \Redis::REDIS_HASH) {
				$this->_redis()->hSet($key, "默认KEY请删除", "默认VALUE请删除");
			}
		}
		else if ($type == "list") {
			if ($oldType != \Redis::REDIS_NOT_FOUND && $oldType != \Redis::REDIS_LIST) {
				$this->_redis()->delete($key);
			}

			if ($oldType != \Redis::REDIS_LIST) {
				$this->_redis()->rPush($key, "默认VALUE请删除");
			}
		}
		else if ($type == "set") {
			if ($oldType != \Redis::REDIS_NOT_FOUND && $oldType != \Redis::REDIS_SET) {
				$this->_redis()->delete($key);
			}

			if ($oldType != \Redis::REDIS_SET) {
				$this->_redis()->sAdd($key, "默认VALUE请删除");
			}
		}
		else if ($type == "zset") {
			if ($oldType != \Redis::REDIS_NOT_FOUND && $oldType != \Redis::REDIS_ZSET) {
				$this->_redis()->delete($key);
			}

			if ($oldType != \Redis::REDIS_ZSET) {
				$this->_redis()->zAdd($key, 1, "默认VALUE请删除");
			}
		}

		//设置超时时间
		if ($ttl < 0) {
			$this->_redis()->persist($key);
		}
		else {
			$timestamp = $dateHelper->timeAfter($timeCount, $timeType);
			$this->_redis()->expireAt($key, $timestamp);
		}

		$this->next("@.doc.updateForm", [
			"key" => $key,
			"g" => u("@.server.index", [ "serverId" => $this->_server->id ]),
			"serverId" => $this->_server->id
		]);
	}
}

?>