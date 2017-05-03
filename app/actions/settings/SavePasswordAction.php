<?php

namespace app\actions\settings;

use app\models\user\User;
use tea\Must;

class SavePasswordAction extends BaseAction {
	public function run(string $pass, string $newPass, string $newPass2, Must $must) {
		//校验
		$must
			->field("pass", $pass)
			->require("请输入当前密码")

			->field("newPass", $newPass)
			->require("请输入新密码")

			->field("newPass2", $newPass2)
			->require("请重新输入新密码")
			->equal($newPass, "两次输入的密码不一致");

		//修改
		User::updateUserPassword($this->userId(), $newPass);

		//刷新页面
		$this->refresh()->success("保存成功");
	}
}

?>