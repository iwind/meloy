<?php

namespace app\classes;

class RSA {
	private static $_types = [];
	private $_privateKeyFile = null;
	private $_publicKeyFile = null;

	/**
	 * @param string $type
	 * @return self
	 */
	public static function rsa($type = "common") {
		if (!isset(self::$_types[$type])) {
			self::$_types[$type] = new self($type);
		}
		return self::$_types[$type];
	}

	public function __construct($type) {
		$config = o("rsa.{$type}");
		$this->_privateKeyFile = $config["private"];
		$this->_publicKeyFile = $config["public"];
	}

	public function sign($data) {
		$privateKey = file_get_contents($this->_privateKeyFile);

		$res = openssl_get_privatekey($privateKey);
		openssl_sign($data, $sign, $res);
		openssl_free_key($res);

		$sign = base64_encode($sign);
		return $sign;
	}

	public function verify($data, $sign)  {
		$pubicKey = file_get_contents($this->_publicKeyFile);
		$res = openssl_get_publickey($pubicKey);
		$result = (bool)openssl_verify($data, base64_decode($sign), $res);
		openssl_free_key($res);
		return $result;
	}

	public function encrypt($data) {
		if (!function_exists("openssl_get_publickey")) {
			return base64_encode($data);
		}
		$publicKey = file_get_contents($this->_publicKeyFile);

		$res = openssl_get_publickey($publicKey);
		openssl_public_encrypt($data, $encryptData, $res);
		openssl_free_key($res);

		$encryptData = base64_encode($encryptData);
		return $encryptData;
	}

	public function decrypt($encryptedData) {
		if (!function_exists("openssl_get_privatekey")) {
			return base64_decode($encryptedData);
		}
		$privateKey = file_get_contents($this->_privateKeyFile);
		$res = openssl_get_privatekey($privateKey);

		$content = base64_decode($encryptedData, false);
		$result  = "";
		$segments = strlen($content)/128;
		for($i = 0; $i < $segments; $i++  ) {
			$data = substr($content, $i * 128, 128);
			openssl_private_decrypt($data, $decrypt, $res);
			$result .= $decrypt;
		}
		openssl_free_key($res);
		return $result;
	}

	public function encryptArray(array $data) {
		return self::encrypt(serialize($data));
	}

	public function decryptArray($data) {
		$s = unserialize(self::decrypt($data));

		if (!is_array($s)) {
			$s = [];
		}

		return $s;
	}
}

?>