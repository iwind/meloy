<?php

namespace app\actions\settings;

use app\models\user\User;
use tea\Must;

class SaveEmailAction extends BaseAction {
	public function run(string $email, Must $must) {
		//校验参数
		$must->field("email", $email)
			->require("请输入登录邮箱")
			->email("请输入正确的邮箱");

		//判断邮箱是否已被使用
		$userId = User::findUserIdWithEmail($email);
		if ($userId > 0 && $userId != $this->userId()) {
			$this->field("email", "该邮箱已经被别的用户使用，请换一个")->fail();
		}

		//更改用户邮箱
		User::updateUserEmail($this->userId(), $email);

		$this->refresh()->success("保存成功");
	}
}

?>