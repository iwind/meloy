<?php

namespace app\actions\team\member;

use app\actions\team\BaseAction;
use app\models\server\Server;
use app\specs\OperationSpec;
use app\specs\TableSpec;
use es\app\specs\DbSpec;

class PermissionTablesAction extends BaseAction {
	public function run(int $serverId, string $db) {
		$this->validateAdmin();

		$server = Server::find($serverId);
		if (!$server) {
			return 404;
		}

		if (is_empty($db)) {
			return 404;
		}

		$spec = new DbSpec($server);
		$spec->name($db);

		$this->data->tables = array_map(function (TableSpec $tableSpec) {
			return (object)[
				"name" => $tableSpec->name()
			];
		}, $spec->tables());
		$this->data->operations = array_map(function (OperationSpec $operationSpec) {
			return (object)[
				"name" => $operationSpec->name(),
				"code" => $operationSpec->code()
			];
		}, $spec->operations());
	}
}

?>