<?php

namespace app\models\user;

use \tea\db\Model;

/**
 * 用户
 */
class User extends Model {
	public static $TABLE = "%{prefix}users";
	public static $VERSION = "1.0";

	const STATE_DISABLED = 0; // 禁用
	const STATE_ENABLED = 1; // 启用


	/**
	 * ID
	 */
	public $id;

	/**
	 * 手机号
	 */
	public $mobile;

	/**
	 * 昵称
	 */
	public $nickname;

	/**
	 * 登录邮箱
	 */
	public $email;

	/**
	 * 密码
	 */
	public $password;

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
	 * 启用条目
	 * @param int $userId 条目ID
	 */
	public static function enableUser($userId) {
		self::query()
			->pk($userId)
			->save([
				"state" => self::STATE_ENABLED
			]);
	}

	/**
	 * 禁用条目
	 * @param int $userId 条目ID
	 */
	public static function disableUser($userId) {
		self::query()
			->pk($userId)
			->save([
				"state" => self::STATE_DISABLED
			]);
	}

	/**
	 * 查找启用的条目
	 *
	 * @param int $userId 条目ID
	 * @return self
	 */
	public static function findEnabledUser($userId) {
		return self::query()
			->pk($userId)
			->state(self::STATE_ENABLED)
			->find();
	}

	/**
	 * 生成第一个用户
	 */
	public static function genFirstUser() {
		if (self::query()->exist()) {
			return;
		}

		$user = new User();
		$user->email = "root@putdata.cn";
		$user->password = self::genPassword("123456");
		$user->nickname = "管理员";
		$user->state = self::STATE_ENABLED;
		$user->save();
	}

	/**
	 * 生成混淆后的密码
	 *
	 * @param string $origin 原始密码
	 * @return string
	 */
	public static function genPassword($origin) {
		return md5(o("app.secret") . "@" . $origin);
	}

	/**
	 * 根据邮箱和密码查找用户
	 *
	 * @param string $email 邮箱地址
	 * @param string $password 密码
	 * @return self
	 */
	public static function findUserWithEmailAndPassword($email, $password) {
		return self::query()
			->attr("email", $email)
			->attr("password", self::genPassword($password))
			->find();
	}

	/**
	 * 修改用户密码
	 *
	 * @param int $userId 用户ID
	 * @param string $password 密码
	 */
	public static function updateUserPassword($userId, $password) {
		self::query()
			->pk($userId)
			->save([
				"password" => self::genPassword($password)
			]);
	}

	/**
	 * 修改用户信息
	 *
	 * @param int $userId 用户ID
	 * @param string $nickname 昵称
	 */
	public static function updateUser($userId, $nickname) {
		self::query()
			->pk($userId)
			->save([
				"nickname" => $nickname
			]);
	}
}

?>