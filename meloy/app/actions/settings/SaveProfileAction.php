<?php

namespace app\actions\settings;

use app\models\user\User;
use tea\Must;

class SaveProfileAction extends BaseAction {
	public function run(string $nickname, Must $must) {
		//校验
		$must->field("nickname", $nickname)
			->require("请输入昵称")
			->maxLength(30, "昵称不能超过30个字符");

		//修改资料
		User::updateUser($this->userId(), $nickname);

		//刷新
		$this->refresh()->success("保存成功");
	}
}

?>