<?php

namespace app\actions\team;

use app\models\team\Team;
use app\models\team\TeamUser;
use tea\Must;

/**
 * 创建团队
 */
class CreateAction extends BaseAction {
	public function run(string $name, Must $must) {
		//校验
		$must->field("name", $name)
			->require("请输入团队名称")
			->maxLength(30, "团队名称不能超出30个字符");

		//是否已有团队
		if ($this->_team) {
			$this->field("name", "你已属于某个团队，不能重复创建团队")->fail();
		}

		//保存
		$teamId = Team::createTeam($this->userId(), $name);

		//加入团队
		TeamUser::createTeamUser($teamId, $this->userId(), true);

		$this->next(".index")->success("创建成功");
	}
}

?>