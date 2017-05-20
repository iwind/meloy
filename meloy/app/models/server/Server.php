<?php

namespace app\models\server;

use es\api\Api;
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
	 * 获取API
	 *
	 * @param API类名
	 * @return Api
	 */
	public function api($class) {
		$options = json_decode($this->options);
		$scheme = "http";
		if ($options != null && is_object($options) && isset($options->scheme)) {
			$scheme = $options->scheme;
		}

		$prefix = "{$scheme}://" . $this->host . ":" . $this->port;

		/**
		 * @var Api $obj
		 */
		$obj = new $class;
		$obj->prefix($prefix);

		return $obj;
	}

	/**
	 * 获取模块名
	 *
	 * @return string
	 */
	public function module() {
		return "@" . ServerType::findTypeCodeWithId($this->typeId);
	}

	/**
	 * 获取类型名
	 *
	 * @return string
	 */
	public function typeName() {
		return ServerType::findTypeName($this->typeId);
	}

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
	 * @param mixed $options 配置选项
	 * @return int
	 */
	public static function createServer($userId, $typeId, $name, $host, $port, $options = null) {
		$server = new self;
		$server->userId = $userId;
		$server->typeId = $typeId;
		$server->name = $name;
		$server->host = $host;
		$server->port = $port;
		$server->state = self::STATE_ENABLED;

		if (!is_null($options)) {
			$server->options = json_encode($options);
		}

		$server->save();

		return $server->id;
	}

	/**
	 * 更改主机信息
	 *
	 * @param int $serverId 主机ID
	 * @param string $name 名称
	 * @param string $host 地址
	 * @param int $port 端口
	 * @param mixed $options 配置选项
	 */
	public static function updateServer($serverId, $name, $host, $port, $options = null) {
		$server = new self;
		$server->id = $serverId;
		$server->name = $name;
		$server->host = $host;
		$server->port = $port;

		if (!is_null($options)) {
			$server->options = json_encode($options);
		}

		$server->save();
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
			->state(self::STATE_ENABLED)
			->asc()
			->findAll();
	}

	/**
	 * 获取用户添加的所有主机
	 *
	 * @param int $userId 用户ID
	 * @return self[]
	 */
	public static function findAllUserServers($userId) {
		return self::query()
			->attr("userId", $userId)
			->state(self::STATE_ENABLED)
			->asc()
			->findAll();
	}
}

?>