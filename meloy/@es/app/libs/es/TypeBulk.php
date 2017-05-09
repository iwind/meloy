<?php

namespace es;

use es\scripts\Script;

class TypeBulk extends Bulk {
	private $_index;
	private $_type;

	public function __construct($index, $type) {
		$this->_index = $index;
		$this->_type = $type;
	}

	public function index($id, array $data) {
		$this->addAction(self::ACTION_INDEX, $this->_index, $this->_type, $id, $data);
	}

	public function create($id, array $data) {
		$this->addAction(self::ACTION_CREATE, $this->_index, $this->_type, $id, $data);
	}

	public function delete($id) {
		$this->addAction(self::ACTION_DELETE, $this->_index, $this->_type, $id);
	}

	public function update($id, array $data, array $options = []) {
		$this->addAction(self::ACTION_UPDATE, $this->_index, $this->_type, $id, [
			"doc" => $data
		], $options);
	}

	public function updateScript($id, Script $script, array $upsert = [], array $options = []) {
		$this->addAction(self::ACTION_UPDATE, $this->_index, $this->_type, $id, [
			"script" => $script->asArray(),
			"upsert" => empty($upsert) ? null : $upsert,
		], $options);
	}

	public function upsert($id, array $data, array $options = []) {
		$this->addAction(self::ACTION_UPDATE, $this->_index, $this->_type, $id, [
			"doc" => $data,
			"doc_as_upsert" => true
		], $options);
	}
}

?>