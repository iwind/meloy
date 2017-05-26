<?php

namespace helpers\app\actions\string;

use app\classes\AuthAction;

/**
 * 执行转换
 */
class ConvertAction extends AuthAction {
	public function run(string $fn, string $origin) {
		$this->data->result = "";

		switch ($fn) {
			case "":
				$this->data->result = "";
				break;
			case "md5":
				$this->data->result = md5($origin);
				break;
			case "sha1":
				$this->data->result = sha1($origin);
				break;
			case "crc32":
				$this->data->result = sprintf("%u", crc32($origin));
				break;
			case "base64_encode":
				$this->data->result = base64_encode($origin);
				break;
			case "base64_decode":
				$this->data->result = base64_decode($origin);
				break;
			case "urlencode":
				$this->data->result = urlencode($origin);
				break;
			case "urldecode":
				$this->data->result = urldecode($origin);
				break;
			case "htmlspecialchars":
				$this->data->result = htmlspecialchars($origin);
				break;
			case "htmlspecialchars_decode":
				$this->data->result = htmlspecialchars_decode($origin);
				break;
		}
	}
}

?>