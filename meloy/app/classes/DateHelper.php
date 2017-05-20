<?php

namespace app\classes;

/**
 * 日期/时间相关辅助方法
 */
class DateHelper {
	/**
	 * 从周N得到对应的字符串
	 *
	 * @param int $index 周N，从0-6
	 * @return string
	 */
	public function weekday($index) {
		return [
			"周日", "周一", "周二", "周三", "周四", "周五", "周六"
		][$index];
	}

	/**
	 * 转换date("w")的结果
	 *
	 * @param int $index date("w")的结果
	 * @return int
	 */
	public function weekIndex($index) {
		if ($index == 0) {
			return 6;
		}
		return $index - 1;
	}

	/**
	 * 格式化时间戳
	 *
	 * @param int $timestamp 时间戳
	 * @return string
	 */
	public function humanFormat($timestamp) {
		$now = time();
		if ($now - $timestamp < 60) {
			return "刚刚";
		}
		else if ($now - $timestamp < 3600) {
			return floor(($now - $timestamp) / 60) . "分钟前";
		}
		else if ($now - $timestamp < 86400) {
			return floor(($now - $timestamp) / 3600) . "小时前";
		}
		else if ($now - $timestamp < 86400 * 30) {
			return floor(($now - $timestamp) / 86400) . "天前";
		}
		else {
			return date("Y-m-d H:i", $timestamp);
		}
	}

	/**
	 * 格式化时间
	 *
	 * @param int $seconds 秒数
	 * @return string
	 */
	public function format($seconds) {
		$days = floor($seconds / 86400);
		$hours = floor(($seconds % 86400) / 3600);
		$minutes = floor(($seconds % 3600) / 60);
		$seconds = $seconds % 60;

		$format = "";
		if ($days > 0) {
			$format .= $days . "天";
		}
		if ($hours > 0) {
			$format .= $hours . "小时";
		}
		if ($minutes > 0) {
			$format .= $minutes . "分钟";
		}
		if ($seconds > 0) {
			$format .= $seconds . "秒";
		}

		if (is_empty($format)) {
			$format = $seconds . "秒";
		}

		return $format;
	}

	/**
	 * 取得当前时间之后N长时间之后的时间戳
	 *
	 * @param int $count 时间数
	 * @param string $type 类型
	 * @return int
	 */
	public function timeAfter($count, $type) {
		return strtotime("now +{$count} {$type}s");
	}
}

?>