<?php

namespace app\models\user;

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

}

?>