<?php

namespace app\specs;

abstract class ModuleSpec {
	protected $_name;
	protected $_menuName;
	protected $_description;
	protected $_version;
	protected $_visible = true;
	protected $_icon;
	protected $_developer;
	protected $_helpers = [];

	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}
		$this->_name = $name;
		return $this;
	}

	public function menuName($name = nil) {
		if (is_nil($name)) {
			return $this->_menuName;
		}
		$this->_menuName = $name;
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

	public function visible($visible = nil) {
		if (is_nil($visible)) {
			return $this->_visible;
		}
		$this->_visible = $visible;
		return $this;
	}

	public function icon($icon = nil) {
		if (is_nil($icon)) {
			return $this->_icon;
		}
		$this->_icon = $icon;
		return $this;
	}

	public function developer($developer = nil) {
		if (is_nil($developer)) {
			return $this->_developer;
		}
		$this->_developer = $developer;
		return $this;
	}

	/**
	 * 取得模块自带的小助手
	 *
	 * @return HelperSpec[]
	 */
	public function helpers() {
		return $this->_helpers;
	}

	/**
	 * 根据模块名称加载模块规约类
	 *
	 * @param string $module 模块名称
	 * @return self|null
	 */
	public static function new($module) {
		$className = $module . "\\app\\specs\\ModuleSpec";
		if (class_exists($className)) {
			return new $className;
		}
		return null;
	}
}

?>