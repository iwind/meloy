<?php

namespace app\models\user;

use \tea\db\Model;

/**
 * 操作记录
 */
class UserLog extends Model {
	public static $TABLE = "%{prefix}userLogs";
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
	 * 操作描述
	 */
	public $description;

	/**
	 * 创建时间
	 */
	public $createdAt;

}

?>