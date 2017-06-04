<?php

namespace app\models\team;

use \tea\db\Model;

/**
 * 用户加入的团队
 */
class TeamUser extends Model {
	public static $TABLE = "%{prefix}teamUsers";
	public static $VERSION = "1.0";

	const STATE_DISABLED = 0; // 禁用
	const STATE_ENABLED = 1; // 启用

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

	/**
	 * 状态
	 */
	public $state;

	/**
	 * 是否为管理员
	 */
	public $isAdmin;

	/**
	 * 取得用户所在的团队ID
	 *
	 * @param int $userId 用户ID
	 * @return int
	 */
	public static function findUserTeamId($userId) {
		return self::query()
			->attr("userId", $userId)
			->result("teamId")
			->findCol(0);
	}

	/**
	 * 加入团队
	 *
	 * @param int $teamId 团队ID
	 * @param int $userId 用户ID
	 * @param bool $isAdmin 是否为管理员
	 */
	public static function createTeamUser($teamId, $userId, $isAdmin) {
		$teamUser = new self;
		$teamUser->teamId = $teamId;
		$teamUser->userId = $userId;
		$teamUser->isAdmin = $isAdmin ? 1 : 0;
		$teamUser->save();
	}

	/**
	 * 计算成员数量
	 *
	 * @param int $teamId 团队ID
	 * @return int
	 */
	public static function countTeamUsers($teamId) {
		return self::query()
			->attr("teamId", $teamId)
			->count();
	}

	/**
	 * 取得所有团队成员
	 *
	 * @param int $teamId 团队ID
	 * @return self[]
	 */
	public static function findTeamUsers($teamId) {
		return self::query()
			->attr("teamId", $teamId)
			->asc()
			->findAll();
	}

	/**
	 * 判断用户是否为管理员
	 *
	 * @param int $teamId 团队ID
	 * @param int $userId 用户ID
	 * @return bool
	 */
	public static function isTeamAdmin($teamId, $userId) {
		return self::query()
			->attr("teamId", $teamId)
			->attr("userId", $userId)
			->result("isAdmin")
			->findCol(0) == 1;
	}

	/**
	 * 判断是否存在团队成员，无论是否已被禁用
	 *
	 * @param int $teamId 团队ID
	 * @param int $userId 用户ID
	 * @return bool
	 */
	public static function existTeamUser($teamId, $userId) {
		return self::query()
			->attr("teamId", $teamId)
			->attr("userId", $userId)
			->exist();
	}

	/**
	 * 启用团队成员
	 *
	 * @param int $teamId 团队ID
	 * @param int $userId 启用用户ID
	 */
	public static function enableTeamUser($teamId, $userId) {
		self::query()
			->attr("teamId", $teamId)
			->attr("userId", $userId)
			->save([
				"state" => self::STATE_ENABLED
			]);
	}

	/**
	 * 禁用团队成员
	 *
	 * @param int $teamId 团队ID
	 * @param int $userId 启用用户ID
	 */
	public static function disableTeamUser($teamId, $userId) {
		self::query()
			->attr("teamId", $teamId)
			->attr("userId", $userId)
			->save([
				"state" => self::STATE_DISABLED
			]);
	}
}

?>