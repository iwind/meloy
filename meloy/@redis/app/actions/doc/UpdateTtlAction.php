<?php

namespace redis\app\actions\doc;

use app\classes\DateHelper;

class UpdateTtlAction extends BaseAction {
	public function run(string $key, int $ttl, int $timeCount, string $timeType, DateHelper $dateHelper) {
		if ($ttl < 0) {
			$this->_redis()->persist($key);
		}
		else {
			$timestamp = $dateHelper->timeAfter($timeCount, $timeType);
			$this->_redis()->expireAt($key, $timestamp);
		}

		$this->refresh()->success("保存成功");
	}
}

?>