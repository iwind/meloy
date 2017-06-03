<?php

namespace api\app\actions\login;

use app\classes\RSA;
use app\models\user\User;
use tea\Action;
use tea\auth\ShouldAuth;
use tea\Must;

/**
 * RSA登录
 */
class TicketAction extends Action {
	public function run(string $ticket, string $go, Must $must, ShouldAuth $userAuth) {
		$this->directive("json");

		if (!function_exists("openssl_sign")) {
			$this->fail("要想使用此功能，需要先安装openssl扩展");
		}

		//$ticket = RSA($time @ $userId @ md5( $time @ $userId @db.secret ))

		$must->field("ticket", $ticket)
			->require("请输入Ticket");

		//校验Ticket
		$rsa = RSA::rsa();
		$ticket = $rsa->decrypt($ticket);
		if (is_empty($ticket)) {
			$this->fail("无效的Ticket");
		}

		$pieces = explode("@", $ticket);
		if (count($pieces) != 3) {
			$this->fail("无效的Ticket");
		}

		$time = intval($pieces[0]);
		$userId = intval($pieces[1]);
		$md5 = $pieces[2];

		if (time() - $time > 5) {
			$this->fail("错误的time");
		}

		if ($userId <= 0) {
			$this->fail("错误的userId");
		}

		if (strlen($md5) != 32 || md5($time . "@" . $userId . "@" . o("db.secret")) != $md5) {
			$this->fail("错误的md5值");
		}

		//用户有没有注册
		$user = User::find($userId);
		if (!$user) {
			User::createUserWithId($userId);
		}

		//让用户登录
		$userAuth->storeAttrs([
			"id" => $userId
		]);

		//跳转
		if (!is_empty($go)) {
			header("Location: " . $go);
		}
		else {
			g("dashboard");
		}
	}
}

?>