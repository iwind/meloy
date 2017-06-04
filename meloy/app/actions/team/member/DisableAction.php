<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\team\TeamUser;
use app\models\user\User;

/**
 * 禁用成员
 */
class DisableAction extends BaseAction {
	public function run(int $userId) {
		$this->validateAdmin();

		$teamUser = TeamUser::existTeamUser($this->_team->id, $userId);
		if ($teamUser) {
			User::disableUser($userId);
		}

		$this->refresh();
	}
}

?>