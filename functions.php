<?php

/**
 * 把JSON中的unicode转换为UTF-8
 *
 * @param string $json 要转换的JSON字符串
 * @return string
 */
function json_unicode_to_utf8($json){
	$json = preg_replace_callback("/\\\u([0-9a-f]{4})/", create_function('$match', '
		$val = intval($match[1], 16);
		$c = "";
		if($val < 0x7F){        // 0000-007F
			$c .= chr($val);
		} elseif ($val < 0x800) { // 0080-0800
			$c .= chr(0xC0 | ($val / 64));
			$c .= chr(0x80 | ($val % 64));
		} else {                // 0800-FFFF
			$c .= chr(0xE0 | (($val / 64) / 64));
			$c .= chr(0x80 | (($val / 64) % 64));
			$c .= chr(0x80 | ($val % 64));
		}
		return $c;
	'), $json);
	return $json;
}

/**
 * 取得对象的属性名数组
 *
 * @param object $object 对象
 * @return array
 */
function object_keys($object) {
	return array_keys((array)$object);
}

?>