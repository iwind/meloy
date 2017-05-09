<?php

namespace es;

class DocMeta {
	private $_id;
	private $_score;

	public function __construct() {

	}

	public function setId($id) {
		$this->_id = $id;
	}

	public function setScore($score) {
		$this->_score = $score;
	}

	public function id() {
		return $this->_id;
	}

	public function score() {
		return $this->_score;
	}
}

?>