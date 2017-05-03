<?php

namespace es;

class Bulk {
	const ACTION_INDEX = "index";
	const ACTION_CREATE = "create";
	const ACTION_DELETE = "delete";
	const ACTION_UPDATE = "update";

	private $_data = [];

	public function __construct() {

	}

	public function addAction($action, $index, $type, $id, array $data = null, array $options = []) {
		$this->_data[] = [
			"action" => $action,
			"index" => $index,
			"type" => $type,
			"id" => $id,
			"data" => $data,
			"options" => $options
		];
	}

	public function actions() {
		return $this->_data;
	}

	public function countActions() {
		return count($this->_data);
	}

	public function clearActions() {
		$this->_data = [];
	}
}

?>