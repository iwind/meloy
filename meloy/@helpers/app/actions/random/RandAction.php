<?php

namespace helpers\app\actions\random;

use app\classes\AuthAction;
use tea\string\Helper;

/**
 * 生成随机字符串
 */
class RandAction extends AuthAction {
	public function run(int $length, bool $containsNumbers, bool $containsLowercase, bool $containsUppercase, bool $containsPunctuation) {
		if ($length <= 0) {
			$length = 32;
		}

		$seed = "";
		if ($containsNumbers) {
			$seed .= implode("", range(0, 9)) . str_replace(".", "", microtime(true));
		}
		if ($containsLowercase) {
			$seed .= implode("", range("a", "z"));
		}
		if ($containsUppercase) {
			$seed .= implode("", range("A", "Z"));
		}
		if ($containsPunctuation) {
			$seed .= '~!@#$%^&*()-_=+|{}[]:;/><,.';
		}

		$seed = str_shuffle(str_repeat($seed, 5));

		if (is_empty($seed)) {
			$this->data->result = "";
			return;
		}

		$this->data->result = Helper::randomString($length, $seed);
	}
}

?>