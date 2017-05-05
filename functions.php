<?php

/**
 * convert unicode in json to utf-8
 *
 * @param string $json string to convert
 * @return string utf-8 string
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

?>