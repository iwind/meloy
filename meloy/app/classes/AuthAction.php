<?php

namespace app\classes;

use app\models\user\User;
use app\models\user\UserSetting;
use app\specs\ModuleSpec;
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

		//@TODO 插件是否被团队管理员禁用

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

		//插件是否被用户禁用
		$disabledModules = UserSetting::findDisabledModuleCodesForUser($this->_userId);
		if (in_array($this->module(), $disabledModules)) {
			exit("The module '" . $this->module() . "' has been disabled by user");
		}

		//提供的模块
		$meloyModules = ModuleSpec::findAllVisibleModulesForUser($this->_userId);
		$this->data->meloyModules = array_map(function (ModuleSpec $module) {
			return (object)[
				"name" => $module->name(),
				"menuName" => $module->menuName(),
				"code" => $module->code(),
				"icon" => $module->icon()
			];
		}, $meloyModules);

		//meloy
		$meloy = new \stdClass();
		$meloy->version = o("meloy.version");
		$this->data->meloy = $meloy;

		//菜单
		$this->data->menu = "@" . $this->module();
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