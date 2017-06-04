<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\team\TeamUser;
use app\models\user\User;
use tea\Must;

/**
 * 保存添加的成员
 */
class CreateAction extends BaseAction {
	public function run(string $email, string $password, string $password2, string $nickname, Must $must) {
		$this->validateAdmin();

		//校验参数
		$must->field("email", $email)
			->minLength(1, "请输入邮箱")
			->maxLength(128, "邮箱长度不能超过128")
			->email("请输入正确的邮箱")
			->if(function ($email) {
				return !User::findUserIdWithEmail($email);
			}, "邮箱账号已经被占用")

			->field("password", $password)
			->require("请输入成员登录密码")
			->minLength(6, "登录密码不能少于6位")
			->maxLength(20, "登录密码不能多于20位")

			->field("password2", $password2)
			->equal($password, "两次输入的密码不一致")

			->field("nickname", $nickname)
			->require("请输入成员昵称")
			->maxLength(20, "成员昵称不能多于20位");

		//保存账号
		$userId = User::createUser($email, $password, $nickname);

		//加入团队
		TeamUser::createTeamUser($this->_team->id, $userId, false);

		//返回
		$this->refresh()->success("成员添加成功");
	}
}

?>