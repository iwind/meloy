<?php

namespace app\specs;

use app\models\server\Server;

/**
 * 数据库规约
 */
abstract class DbSpec {
	protected $_state;
	protected $_name;
	protected $_tableTypeName = "数据表";
	protected $_server;

	public function __construct(Server $server) {
		$this->_server = $server;
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

	public function tableTypeName() {
		return $this->_tableTypeName;
	}

	public function tables() {
		return [];
	}

	/**
	 * 取得所有操作
	 *
	 * @return OperationSpec[]
	 */
	public function operations() {
		return [];
	}
}

?>