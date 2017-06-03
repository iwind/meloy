<?php

namespace app\models\user;

use app\specs\ModuleSpec;
use \tea\db\Model;

/**
 * 用户设置
 */
class UserSetting extends Model {
	public static $TABLE = "%{prefix}userSettings";
	public static $VERSION = "1.0";


	/**
	 * ID
	 */
	public $id;

	/**
	 * 用户ID
	 */
	public $userId;

	/**
	 * 选项名
	 */
	public $name;

	/**
	 * 选项值
	 */
	public $value;

	/**
	 * 取得bool形式的值
	 *
	 * @return bool
	 */
	public function boolValue() {
		return !!json_decode($this->value);
	}

	/**
	 * 取得数组形式的值
	 *
	 * @return array
	 */
	public function arrayValue() {
		$arr = json_decode($this->value, true);
		if (!is_array($arr)) {
			$arr = [];
		}
		return $arr;
	}

	/**
	 * 根据ID查找名称
	 *
	 * @param int $settingId 条目ID
	 * @return string
	 */
	public static function findSettingName($settingId) {
		return self::query()
			->pk($settingId)
			->result("name")
			->findCol("");
	}

	/**
	 * 取得用户设置
	 *
	 * @param int $userId 用户ID
	 * @param string $name 选项名
	 * @return self
	 */
	public static function findUserSetting($userId, $name) {
		return self::query()
			->attr("userId", $userId)
			->attr("name", $name)
			->find();
	}

	/**
	 * 更新用户设置
	 *
	 * @param int $userId 用户ID
	 * @param string $name 选项名
	 * @param mixed $value 选项值
	 */
	public static function updateUserSetting($userId, $name, $value) {
		$setting = self::query()
			->attr("userId", $userId)
			->attr("name", $name)
			->find();
		if (!$setting) {
			$setting = new self;
			$setting->userId = $userId;
			$setting->name = $name;
		}
		$setting->value = $value;
		$setting->save();
	}

	/**
	 * 取得所有禁用的插件代号
	 *
	 * @param int $userId 用户ID
	 * @return array
	 */
	public static function findDisabledModuleCodesForUser($userId) {
		$setting = UserSetting::findUserSetting($userId, "user.modules.disabled");
		return $setting ? $setting->arrayValue() : [];
	}

	/**
	 * 判断插件是否已经初始化
	 *
	 * @param string $module 插件代号
	 * @return bool
	 */
	public static function moduleIsInitialized($module) {
		$spec = ModuleSpec::new($module);
		$version = $spec ? $spec->version() : null;
		if (is_empty($version)) {
			$version = "0.0.1";
		}
		$name = "modules.{$module}.{$version}.initialized";
		$setting = self::findUserSetting(0, $name);
		if (!$setting) {
			return false;
		}
		return $setting->boolValue();
	}

	/**
	 * 设置插件是否已经初始化
	 *
	 * @param string $module 插件代号
	 * @param $bool
	 */
	public static function updateModuleIsInitialized($module, $bool = true) {
		$spec = ModuleSpec::new($module);
		$version = $spec ? $spec->version() : null;
		if (is_empty($version)) {
			$version = "0.0.1";
		}
		$name = "modules.{$module}.{$version}.initialized";
		self::updateUserSetting(0, $name, json_encode($bool));
	}
}

?>