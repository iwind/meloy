<?php

namespace app\classes;

use app\models\server\ServerType;
use app\models\user\User;
use tea\Action;
use tea\auth\Exception;
use tea\auth\MustAuth;

class AuthAction extends Action {
	private $_userId = 0;

	/**
	 * 登录拦截
	 */
	public function before() {
		parent::before();

		//是否已经登录
		try {
			$auth = MustAuth::newForParam("userAuth");

			$userId = $auth->attr("id");

			//是否有效
			$user = User::find($userId);
			if (!$user || $user->state != User::STATE_ENABLED) {
				$auth->unset();
				g("index");
			}
			$this->_userId = $user->id;

			//设置模板中可用的变量
			$this->data->loginUserName = $user->nickname;
		} catch (Exception $e) {
			g("index", [ "g" => $_SERVER["REQUEST_URI"] ]);
		}

		//提供的模块
		$serverTypes = ServerType::findAllEnabledTypes();
		$this->data->serverTypes = array_map(function (ServerType $serverType) {
			return (object)[
				"id" => $serverType->id,
				"name" => $serverType->name,
				"code" => $serverType->code
			];
		}, $serverTypes);

		//meloy
		$meloy = new \stdClass();
		$meloy->version = o("meloy.version");
		$this->data->meloy = $meloy;
	}

	/**
	 * 取得当前用户的ID
	 *
	 * @return int
	 */
	public function userId() {
		return $this->_userId;
	}
}

?>