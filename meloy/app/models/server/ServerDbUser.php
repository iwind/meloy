<?php

namespace app\models\server;

use \tea\db\Model;

/**
 * 表权限设置
 */
class ServerDbUser extends Model {
	public static $TABLE = "%{prefix}serverDbUsers";
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
	 * 主机ID
	 */
	public $serverId;

	/**
	 * 数据库名
	 */
	public $db;

	/**
	 * 允许的操作列表：insert,update,read,delete,drop,alter,create
	 */
	public $allow;

}

?>