<?php

namespace app\models\team;

use \tea\db\Model;

/**
 * 用户加入的团队
 */
class TeamUser extends Model {
	public static $TABLE = "%{prefix}teamUsers";
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
	 * 团队ID
	 */
	public $teamId;

	/**
	 * 加入时间
	 */
	public $createdAt;

}

?>