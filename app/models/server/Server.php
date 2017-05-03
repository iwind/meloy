<?php

namespace app\models\server;

use \tea\db\Model;

/**
 * 主机服务器
 */
class Server extends Model {
	public static $TABLE = "%{prefix}servers";
	public static $VERSION = "1.0";

	const STATE_DISABLED = 0; // 禁用
	const STATE_ENABLED = 1; // 启用


	/**
	 * ID
	 */
	public $id;

	/**
	 * 添加者用户ID
	 */
	public $userId;

	/**
	 * 类型：1 ES，2 MongoDB，3 Redis
	 */
	public $typeId;

	/**
	 * 名称
	 */
	public $name;

	/**
	 * 地址
	 */
	public $host;

	/**
	 * 端口
	 */
	public $port;

	/**
	 * 其他参数，用JSON数据格式表示
	 */
	public $options;

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
	 * @param int $serverId 条目ID
	 * @return string
	 */
	public static function findServerName($serverId) {
		return self::query()
			->pk($serverId)
			->result("name")
			->findCol("");
	}

	/**
	 * 启用条目
	 * @param int $serverId 条目ID
	 */
	public static function enableServer($serverId) {
		self::query()
			->pk($serverId)
			->save([
				"state" => self::STATE_ENABLED
			]);
	}

	/**
	 * 禁用条目
	 * @param int $serverId 条目ID
	 */
	public static function disableServer($serverId) {
		self::query()
			->pk($serverId)
			->save([
				"state" => self::STATE_DISABLED
			]);
	}

	/**
	 * 查找启用的条目
	 *
	 * @param int $serverId 条目ID
	 * @return self
	 */
	public static function findEnabledServer($serverId) {
		return self::query()
			->pk($serverId)
			->state(self::STATE_ENABLED)
			->find();
	}
}

?>