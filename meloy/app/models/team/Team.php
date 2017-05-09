<?php

namespace app\models\team;

use \tea\db\Model;

/**
 * 团队
 */
class Team extends Model {
	public static $TABLE = "%{prefix}teams";
	public static $VERSION = "1.0";

	const STATE_DISABLED = 0; // 禁用
	const STATE_ENABLED = 1; // 启用


	/**
	 * ID
	 */
	public $id;

	/**
	 * 创建者用户ID
	 */
	public $userId;

	/**
	 * 名称
	 */
	public $name;

	/**
	 * 创建时间
	 */
	public $createdAt;

	/**
	 * 状态：1启用，0禁用
	 *
	 * @var int
	 */
	public $state;

	/**
	 * 根据ID查找名称
	 *
	 * @param int $teamId 条目ID
	 * @return string
	 */
	public static function findTeamName($teamId) {
		return self::query()
			->pk($teamId)
			->result("name")
			->findCol("");
	}

	/**
	 * 启用条目
	 * @param int $teamId 条目ID
	 */
	public static function enableTeam($teamId) {
		self::query()
			->pk($teamId)
			->save([
				"state" => self::STATE_ENABLED
			]);
	}

	/**
	 * 禁用条目
	 * @param int $teamId 条目ID
	 */
	public static function disableTeam($teamId) {
		self::query()
			->pk($teamId)
			->save([
				"state" => self::STATE_DISABLED
			]);
	}

	/**
	 * 查找启用的条目
	 *
	 * @param int $teamId 条目ID
	 * @return self
	 */
	public static function findEnabledTeam($teamId) {
		return self::query()
			->pk($teamId)
			->state(self::STATE_ENABLED)
			->find();
	}
}

?>