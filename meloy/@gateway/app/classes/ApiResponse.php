<?php

namespace gateway\app\classes;

class ApiResponse {
	private $_code;
	private $_message;
	private $_data;

	public function __construct() {
	}

	/**
	 * @return mixed
	 */
	public function code() {
		return $this->_code;
	}

	/**
	 * @param mixed $code
	 */
	public function setCode($code) {
		$this->_code = $code;
	}

	/**
	 * @return mixed
	 */
	public function message() {
		return $this->_message;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage($message) {
		$this->_message = $message;
	}

	/**
	 * @return mixed
	 */
	public function data() {
		return $this->_data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data) {
		$this->_data = $data;
	}

	/**
	 * 判断是否为成功的响应
	 *
	 * @return bool
	 */
	public function success() {
		return $this->code() == 200;
	}
}

?>