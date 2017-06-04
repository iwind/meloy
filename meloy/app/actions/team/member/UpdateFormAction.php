<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\team\TeamUser;
use app\models\user\User;

/**
 * 修改用户表单
 */
class UpdateFormAction extends BaseAction {
	public function run(int $userId) {
		$this->validateAdmin();

		if (!TeamUser::existTeamUser($this->_team->id, $userId)) {
			return 404;
		}

		$user = User::find($userId);
		$this->data->user = $user->asPlain([ "id", "email", "nickname" ]);
	}
}

?>