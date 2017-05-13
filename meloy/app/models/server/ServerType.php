<?php

namespace app\models\server;

use \tea\db\Model;

/**
 * 主机类型
 */
class ServerType extends Model {
	public static $TABLE = "%{prefix}serverTypes";
	public static $VERSION = "1.0";

	const STATE_DISABLED = 0; // 禁用
	const STATE_ENABLED = 1; // 启用

	/**
	 * ID
	 */
	public $id;

	/**
	 * 类型名
	 */
	public $name;

	/**
	 * 代号，如es,redis,mongo
	 */
	public $code;

	/**
	 * 排序
	 */
	public $order;

	/**
	 * 状态：0禁用，1启用 
	 *
	 * @var int
	 */
	public $state;

	/**
	 * 根据ID查找名称
	 *
	 * @param int $typeId 条目ID
	 * @return string
	 */
	public static function findTypeName($typeId) {
		return self::query()
			->pk($typeId)
			->result("name")
			->findCol("");
	}

	/**
	 * 启用条目
	 *
	 * @param int $typeId 条目ID
	 */
	public static function enableType($typeId) {
		self::query()
			->pk($typeId)
			->save([
				"state" => self::STATE_ENABLED
			]);
	}

	/**
	 * 禁用条目
	 *
	 * @param int $typeId 条目ID
	 */
	public static function disableType($typeId) {
		self::query()
			->pk($typeId)
			->save([
				"state" => self::STATE_DISABLED
			]);
	}

	/**
	 * 查找启用的条目
	 *
	 * @param int $typeId 条目ID
	 * @return self
	 */
	public static function findEnabledType($typeId) {
		return self::query()
			->pk($typeId)
			->state(self::STATE_ENABLED)
			->find();
	}

	/**
	 * 取得所有可用的类型
	 *
	 * @return self[]
	 */
	public static function findAllEnabledTypes() {
		return self::query()
			->state(self::STATE_ENABLED)
			->desc("order")
			->asc()
			->findAll();
	}

	/**
	 * 根据代号查找类型ID
	 *
	 * @param string $code 代号
	 * @return int
	 */
	public static function findTypeIdWithCode($code) {
		return self::query()
			->resultPk()
			->attr("code", $code)
			->findCol(0);
	}

	/**
	 * 根据类型ID查找类型代号
	 *
	 * @param int $typeId 类型ID
	 * @return string
	 */
	public static function findTypeCodeWithId($typeId) {
		return self::query()
			->pk($typeId)
			->result("code")
			->findCol();
	}

	/**
	 * 创建主机类型
	 *
	 * @param string $name 类型名称
	 * @param string $code 类型代号，如es、redis
	 * @param int $state 状态
	 */
	public static function createServerType($name, $code, $state = self::STATE_ENABLED) {
		$type = new self;
		$type->name = $name;
		$type->state = $state;
		$type->code = $code;
		$type->save();
	}

	/**
	 * 根据类型代号来判断主机类型是否已存在
	 *
	 * @param string $code 主机类型代号
	 * @return bool
	 */
	public static function existServerType($code) {
		return self::query()
			->attr("code", $code)
			->exist();
	}
}

?>