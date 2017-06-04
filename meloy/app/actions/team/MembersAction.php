<?php

namespace app\actions\team;

use app\models\team\Team;
use app\models\team\TeamUser;
use app\models\user\User;

/**
 * 团队成员
 */
class MembersAction extends BaseAction {
	public function run() {
		$this->validateTeam();

		$team = Team::find($this->_team->id);

		$teamUsers = TeamUser::findTeamUsers($this->_team->id);
		$members = [];
		foreach ($teamUsers as $teamUser) {
			$user = User::find($teamUser->userId);
			if (!$user) {
				continue;
			}

			$members[] = (object)[
				"id" => $user->id,
				"nickname" => $user->nickname,
				"email" => $user->email,
				"joinedAt" => date("Y-m-d H:i:s", $teamUser->createdAt),
				"isAdmin" => $teamUser->isAdmin,
				"isCreator" => $teamUser->userId == $team->userId,
				"state" => $user->state
			];
		}

		$this->data->members = $members;
	}
}

?>