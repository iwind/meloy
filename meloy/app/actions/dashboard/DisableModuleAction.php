<?php

namespace app\actions\dashboard;

use app\models\user\UserSetting;

/**
 * 禁用插件
 */
class DisableModuleAction extends BaseAction {
	public function run(string $code) {
		$modules = UserSetting::findDisabledModuleCodesForUser($this->userId());
		if (!in_array($code, $modules)) {
			$modules[] = $code;
		}
		UserSetting::updateUserSetting($this->userId(), "user.modules.disabled", json_encode($modules));

		$this->refresh();
	}
}

?>