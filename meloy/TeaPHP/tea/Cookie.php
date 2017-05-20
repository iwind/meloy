<?php

namespace tea;

class Cookie {
	private $_name;
	private $_path = "/";
	private $_expiredAt;
	private $_domain;
	private $_secure;
	private $_httponly;

	public static function newForParam($param) {
		return new self($param);
	}

	public function __construct($name) {
		$this->_name = $name;
	}

	public function path($path) {
		$this->_path = $path;
		return $this;
	}

	public function expiredAt($expiredAt) {
		$this->_expiredAt = $expiredAt;
		return $this;
	}

	public function life($life) {
		$this->_expiredAt = time() + $life;
		return $this;
	}

	public function domain($domain) {
		$this->_domain = $domain;
		return $this;
	}

	public function secure($secure) {
		$this->_secure = $secure;
		return $this;
	}

	public function httponly($httponly) {
		$this->_httponly = $httponly;
		return $this;
	}

	public function value($default = "") {
		return cookie($this->_name, $default);
	}

	public function set($value) {
		set_cookie($this->_name, $value, $this->_expiredAt, $this->_path, $this->_domain, $this->_secure, $this->_httponly);
		return $this;
	}
}

?>