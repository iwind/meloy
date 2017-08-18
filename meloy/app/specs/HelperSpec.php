<?php

namespace app\specs;

/**
 * 小助手规约
 */
class HelperSpec {
	const SIZE_SMALL = "small";
	const SIZE_MIDDLE = "middle";
	const SIZE_LARGE = "large";

	protected $_name;
	protected $_code;
	protected $_icon;
	protected $_url;
	protected $_size = self::SIZE_SMALL;
	protected $_description;
	protected $_version;

	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}
		$this->_name = $name;
		return $this;
	}

	public function code($code = nil) {
		if (is_nil($code)) {
			return $this->_code;
		}
		$this->_code = $code;
		return $this;
	}

	public function description($description = nil) {
		if (is_nil($description)) {
			return $this->_description;
		}
		$this->_description = $description;
		return $this;
	}

	public function version($version = nil) {
		if (is_nil($version)) {
			return $this->_version;
		}
		$this->_version = $version;
		return $this;
	}

	public function icon($icon = nil) {
		if (is_nil($icon)) {
			return $this->_icon;
		}
		$this->_icon = $icon;
		return $this;
	}

	public function url($url = nil) {
		if (is_nil($url)) {
			return $this->_url;
		}
		$this->_url = $url;
		return $this;
	}

	public function size($size = nil) {
		if (is_nil($size)) {
			return $this->_size;
		}
		$this->_size = $size;
		return $this;
	}
}

?>