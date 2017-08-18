<?php

namespace gateway\app\specs;

use app\models\server\Server;

class ServerSpec extends \app\specs\ServerSpec {
	/**
	 * @var Server
	 */
	private $_server;

	public function __construct(Server $server) {
		$this->_server = $server;
		$this->_dbTypeName = "应用";
	}

	public function dbs() {
		return [];
	}

	public function operations() {
		return [

		];
	}
}

?>