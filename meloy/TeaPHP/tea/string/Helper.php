<?php

namespace tea\string;

class Helper {
	/**
	 * 生成随机字符串
	 *
	 * @param int $length 长度
	 * @return string 结果字符串
	 */
	public static function randomString($length = 32) {
		$microtime = str_replace(".", "", microtime(true));
		$seed = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" . $microtime);
		$seedLength = strlen($seed);

		$result = "";
		for ($i = 0; $i < $length; $i ++) {
			$result .= $seed[rand(0, $seedLength - 1)];
		}

		return $result;
	}

	/**
	 * 生成唯一ID
	 *
	 * @param string|null $rand 随机字符串
	 * @return string 32位的HEX字符串
	 */
	public static function uniqueId($rand = "") {
		static $uname = null;
		if (!$uname) {
			$uname = php_uname();
		}
		return md5($rand . "@" . $uname . "@" . mt_rand(1, 10000000) . "@" . uniqid(null, true) . "@" . mt_rand(1, 10000000));
	}

	/**
	 * 转换数字ID到字符串
	 *
	 * @param int $intId 数字ID
	 * @param string $mapping 字符映射表
	 * @return string
	 */
	public static function idToString($intId, $mapping = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
		$code = "";
		$size = strlen($mapping);
		while ($intId >= $size) {
			$mod = $intId % $size;
			$intId = (int)floor($intId/$size);

			$code .= $mapping[$mod];
		}
		$code .= $mapping[$intId];
		$code = strrev($code);

		return $code;
	}

	/**
	 * 转换字符串到数字ID
	 *
	 * @param string $stringId 字符串ID
	 * @param string $mapping 字符映射表
	 * @return int
	 */
	public static function stringToId($stringId, $mapping = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
		$length = strlen($stringId);
		if ($length == 0) {
			return 0;
		}
		$mapping = str_split($mapping, 1);
		$mapping = array_flip($mapping);
		$id = 0;
		$size = count($mapping);
		for ($i = 0; $i < $length; $i ++) {
			$char = substr($stringId, $length - $i - 1, 1);
			if (!isset($mapping[$char])) {
				return 0;
			}
			$id += $mapping[$char] * pow($size, $i);
		}
		return $id;
	}

	/**
	 * 是否全部为中文
	 *
	 * @param string $string 要匹配的字符串
	 * @return bool
	 */
	public static function containsChineseOnly($string) {
		return (bool)preg_match("/^\\p{Han}+$/u", $string);
	}

	/**
	 * 是否全部为中文、英文、数字
	 *
	 * @param string $string 要匹配的字符串
	 * @return bool
	 */
	public static function containsChineseAlphaOnly($string) {
		return (bool)preg_match("/^[\\p{Han}\\d+a-z]+$/iu", $string);
	}
}

?>