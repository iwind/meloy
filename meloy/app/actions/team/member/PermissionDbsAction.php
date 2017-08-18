<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\server\Server;
use app\specs\DbSpec;
use app\specs\OperationSpec;
use app\specs\ServerSpec;

/**
 * 权限设置 > 数据库
 */
class PermissionDbsAction extends BaseAction {
	public function run(string $module, int $serverId) {
		$this->validateAdmin();

		$server = Server::find($serverId);
		if (!$server) {
			return 404;
		}

		$spec = ServerSpec::new($module, $server);

		$this->data->dbTypeName = $spec->dbTypeName();

		try {
			$this->data->dbs = array_map(function (DbSpec $spec) {
				return (object)[
					"name" => $spec->name()
				];
			}, $spec->dbs());
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->data->operations = array_map(function (OperationSpec $spec) {
			return (object)[
				"name" => $spec->name(),
				"code" => $spec->code()
			];
		}, $spec->operations());
	}
}

?>