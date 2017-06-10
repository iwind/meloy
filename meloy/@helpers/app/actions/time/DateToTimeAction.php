<?php

namespace helpers\app\actions\time;

use app\classes\AuthAction;

/**
 * 日期转换为时间戳
 */
class DateToTimeAction extends AuthAction {
	public function run(string $date) {
		$time = strtotime($date);
		if ($time === false) {
			$time = "-";
		}
		$this->data->time = $time;
	}
}

?>