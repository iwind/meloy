<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\user\User;
use app\specs\ModuleSpec;

/**
 * 权限设置
 */
class PermissionsAction extends BaseAction {
	public function run(int $userId, string $module) {
		$this->validateAdmin();

		//用户信息
		$user = User::find($userId);
		if (!$user) {
			return 404;
		}
		$this->data->user = $user->asPlain([ "id", "nickname" ]);

		//插件列表
		$this->data->modules = array_map(function (ModuleSpec $spec) {
			//是否有设置权限

			return (object)[
				"code" => $spec->code(),
				"name" => $spec->name()
			];
		}, ModuleSpec::findAllModules());

		if (is_empty($module)) {
			$module = $this->data->modules[0]->code;
		}

		$this->data->selectedModule = $module;
	}
}

?>