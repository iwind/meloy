<?php

namespace app\actions\team;

use app\classes\AuthAction;
use app\models\team\Team;
use app\models\team\TeamUser;
use tea\Request;

class BaseAction extends AuthAction {
	/**
	 * 团队信息
	 *
	 * @var Team
	 */
	protected $_team;

	public function before() {
		parent::before();

		$this->data->menu = "team";

		//是否已经有团队
		$teamId = TeamUser::findUserTeamId($this->userId());
		if ($teamId > 0) {
			$this->_team = Team::find($teamId);
		}

		//团队信息
		$this->data->team = null;
		$this->data->isAdmin = false;
		$isAdmin = false;
		$countMembers = 0;
		if ($this->_team) {
			$isAdmin = TeamUser::isTeamAdmin($teamId, $this->userId());
			$this->data->team = $this->_team->asPlain([ "id", "name" ]);
			$this->data->isAdmin = $isAdmin;
			$countMembers = TeamUser::countTeamUsers($this->_team->id);
		}

		//操作栏
		$request = Request::shared();
		if ($request->isGet()) {
			if ($this->_team) {
				$this->data->tabbar = [
					[
						"name" => "团队",
						"url" => u("team"),
						"active" => $this->name() == "index"
					],
					[
						"name" => "成员({$countMembers})",
						"url" => u("team.members"),
						"active" => ($this->name() == "members" || $this->fullName() == "team.member.createForm" || $this->fullName() == "team.member.updateForm")
					]
				];

				if ($isAdmin) {
					/**$this->data->tabbar[] = [
						"name" => "权限",
						"url" => u("team.perms"),
						"active" => $this->name() == "perms",
					];**/
				}
			}
			else {
				$this->data->tabbar = [
					[
						"name" => "团队",
						"url" => u("team"),
						"active" => true
					]
				];
			}
		}
	}

	/**
	 * 校验团队信息
	 */
	public function validateTeam() {
		if (!$this->_team) {
			exit("没有权限进行此操作");
		}
	}

	/**
	 * 校验团队管理员信息
	 */
	public function validateAdmin() {
		if (!$this->_team || !TeamUser::isTeamAdmin($this->_team->id, $this->userId())) {
			exit("没有权限进行此操作");
		}
	}
}

?>