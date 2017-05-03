<?php

namespace app\actions\login;

use app\models\user\User;
use tea\Action;
use tea\auth\ShouldAuth;
use tea\Must;

class IndexAction extends Action {
	public function run(string $email, string $password, Must $must, ShouldAuth $userAuth) {
		//校验参数
		$must->field("email", $email)
				->minLength(1, "请输入邮箱")
				->maxLength(128, "邮箱长度不能超过128")
				->email("请输入正确的邮箱");
		$must->field("password", $password)
			->minLength(1, "请输入登录密码");

		//校验密码
		$user = User::findUserWithEmailAndPassword($email, $password);
		if (!$user) {
			$this->field("email", "邮箱或密码错误")->fail();
		}

		//用户状态
		if ($user->state != User::STATE_ENABLED) {
			$this->field("email", "该用户已被禁用")->fail();
		}

		//登录
		$userAuth->storeAttrs([
			"id" => $user->id
		]);

		//跳转
		$this->next("dashboard");
	}
}

?>