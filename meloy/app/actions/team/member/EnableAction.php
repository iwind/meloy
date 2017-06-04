<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\team\TeamUser;
use app\models\user\User;

/**
 * 启用成员
 */
class EnableAction extends BaseAction {
	public function run(int $userId) {
		$this->validateAdmin();

		$teamUser = TeamUser::existTeamUser($this->_team->id, $userId);
		if ($teamUser) {
			User::enableUser($userId);
		}

		$this->refresh();
	}
}

?>