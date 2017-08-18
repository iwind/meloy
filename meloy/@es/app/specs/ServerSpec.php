<?php

namespace es\app\specs;

use app\models\server\Server;
use app\specs\OperationSpec;
use es\api\GetIndexApi;
use tea\Tea;

class ServerSpec extends \app\specs\ServerSpec {
	/**
	 * @var Server
	 */
	private $_server;

	public function __construct(Server $server) {
		$this->_server = $server;
		$this->_dbTypeName = "索引";
	}

	public function dbs() {
		//加载ES操作库
		import(Tea::shared()->root() . DS . "@es/app/libs");

		/**
		 * @var GetIndexApi $api
		 */
		$api = $this->_server->api(GetIndexApi::class);
		$dbs = [];
		foreach (object_keys($api->getAll()) as $indexName) {
			$spec = new DbSpec($this->_server);
			$spec->name($indexName);

			$dbs[] = $spec;
		}

		return $dbs;
	}

	public function operations() {
		return [
			new OperationSpec("删除", "delete")
		];
	}
}

?>