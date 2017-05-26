<?php

namespace app\specs;

use app\models\server\Server;

abstract class ServerSpec {
	protected $_state;
	protected $_name;

	public function __construct(Server $server) {

	}

	public function state($state = nil) {
		if (is_nil($state)) {
			return $this->_state;
		}

		$this->_state = $state;
		return $this;
	}

	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}

		$this->_name = $name;
		return $this;
	}

	public function dbs() {
		return [];
	}
}

?>