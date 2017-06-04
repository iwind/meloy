<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\team\TeamUser;
use app\models\user\User;
use tea\Must;

/**
 * 修改用户
 */
class UpdateAction extends BaseAction {
	public function run(int $userId, string $email, string $password, string $password2, string $nickname, Must $must) {
		$this->validateAdmin();

		if (!TeamUser::existTeamUser($this->_team->id, $userId)) {
			return 404;
		}

		//校验参数
		$must->field("email", $email)
			->minLength(1, "请输入邮箱")
			->maxLength(128, "邮箱长度不能超过128")
			->email("请输入正确的邮箱")
			->if(function ($email) use ($userId) {
				$anotherUserId = User::findUserIdWithEmail($email);
				return $anotherUserId == 0 || $anotherUserId == $userId;
			}, "邮箱账号已经被占用");

		if (!is_empty($password)) {
			$must
				->field("password", $password)
				->require("请输入成员登录密码")
				->minLength(6, "登录密码不能少于6位")
				->maxLength(20, "登录密码不能多于20位")
				->field("password2", $password2)
				->equal($password, "两次输入的密码不一致");
		}
		$must
			->field("nickname", $nickname)
			->require("请输入成员昵称")
			->maxLength(20, "成员昵称不能多于20位");

		//保存
		User::updateUser($userId, $nickname);
		User::updateUserEmail($userId, $email);

		if (!is_empty($password)) {
			User::updateUserPassword($userId, $password);
		}

		$this->refresh()->success("保存成功");
	}
}

?>