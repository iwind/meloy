<?php

namespace app\models\server;

use \tea\db\Model;

/**
 * 服务器权限设置
 */
class ServerUser extends Model {
	public static $TABLE = "%{prefix}serverUsers";
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
	 * 允许的操作列表：insert,update,read,delete,drop,alter,create
	 */
	public $allow;

}

?>