<?php

namespace app\actions\install;

use app\models\user\User;
use tea\Must;

class SaveAdminAction extends BaseAction {
	public function run(string $email, string $pass, string $pass2, string $nickname, Must $must) {
		//校验参数
		$must->field("email", $email)
			->require("请输入登录邮箱")
			->email("请输入正确的邮箱")

			->field("pass", $pass)
			->require("请输入登录密码")

			->field("pass2", $pass2)
			->require("请重新输入登录密码")
			->equal($pass, "两次输入的密码不一致")

			->field("nickname", $nickname)
			->require("请输入昵称")
			->maxLength(30, "昵称不能超过30个字符");

		$userId = 1;
		User::updateUser($userId, $nickname);
		User::updateUserEmail($userId, $email);
		User::updateUserPassword($userId, $pass);

		$this->next("@")->success("配置完成，请去登录");
	}
}

?>