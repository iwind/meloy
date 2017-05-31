<?php

namespace app\specs;

use app\models\user\UserSetting;
use tea\file\File;

abstract class ModuleSpec {
	protected $_code;
	protected $_name;
	protected $_menuName;
	protected $_description;
	protected $_version;
	protected $_visible = true;
	protected $_icon;
	protected $_developer;
	protected $_helpers = [];

	public function code($code = nil) {
		if (is_nil($code)) {
			return $this->_code;
		}
		$this->_code = $code;
		return $this;
	}

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
	 * 根据模块代号加载模块规约类
	 *
	 * @param string $module 模块代号
	 * @return self|null
	 */
	public static function new($module) {
		$className = $module . "\\app\\specs\\ModuleSpec";
		if (class_exists($className)) {
			$obj = new $className; /** @var self $obj */
			$obj->code($module);
			return $obj;
		}
		return null;
	}

	/**
	 * 取得所有的模块Spec
	 *
	 * @param int $userId 用户ID
	 * @return self[]
	 */
	public static function findAllVisibleModulesForUser($userId) {
		$disabledModules = UserSetting::findDisabledModuleCodesForUser($userId);

		$dir = new File(TEA_ROOT);
		$results = [];
		$dir->each(function (File $file) use (&$modules, &$results, $disabledModules) {
			if (!$file->isDir()) {
				return;
			}
			$basename = basename($file->path());
			if (preg_match("/@(\\w+)$/", $basename, $match)) {
				$code = $match[1];
				if (in_array($code, $disabledModules)) {
					return;
				}
				$spec = ModuleSpec::new($code);
				if (!$spec->visible()) {
					return;
				}
				$results[] = $spec;
			}
		}, 0);

		return $results;
	}
}

?>