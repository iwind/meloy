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

	const TYPE_ES = 1;
	const TYPE_MONGO = 2;
	const TYPE_REDIS = 3;

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

	/**
	 * 创建主机
	 *
	 * @param int $userId 用户ID
	 * @param int $typeId 类型ID
	 * @param string $name 名称
	 * @param string $host 地址
	 * @param int $port 端口
	 * @return int
	 */
	public static function createServer($userId, $typeId, $name, $host, $port) {
		$server = new self;
		$server->userId = $userId;
		$server->typeId = $typeId;
		$server->name = $name;
		$server->host = $host;
		$server->port = $port;
		$server->state = self::STATE_ENABLED;
		$server->save();

		return $server->id;
	}

	/**
	 * 查找用户添加的某种类型的主机
	 *
	 * @param int $userId 用户ID
	 * @param int $typeId 类型ID
	 * @return self[]
	 */
	public static function findUserServersWithType($userId, $typeId) {
		return self::query()
			->attr("userId", $userId)
			->attr("typeId", $typeId)
			->asc()
			->findAll();
	}
}

?>