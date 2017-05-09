<?php

namespace tea;

class Request {
	private $_params = [];
	private static $_instance;

	/**
	 * 取得共享实例
	 *
	 * @return Request
	 */
	public static function shared() {
		if (self::$_instance == null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public function __construct() {
		//合并参数
		if (isset($_GET) && is_array($_GET)) {
			$this->_params = $_GET;
		}
		if (isset($_POST) && is_array($_POST) && !empty($_POST)) {
			$this->_params = array_merge($this->_params, $_POST);
		}
	}

	public function param($param, $value = nil) {
		if ($value !== nil) {
			$this->_params[$param] = $value;
		}
		return $this->_params[$param] ?? null;
	}

	public function params() {
		return $this->_params;
	}

	public function isAjax() {
		return (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
	}
}

?>