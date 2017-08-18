<?php

namespace app\models\module;

use \tea\db\Model;

/**
 * 模块权限设置
 */
class ModuleUser extends Model {
	public static $TABLE = "%{prefix}moduleUsers";
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
	 * 模块代号
	 */
	public $module;

}

?>