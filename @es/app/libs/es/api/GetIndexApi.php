<?php

namespace es\api;

class GetIndexApi extends Api {
	public function getAll() {
		$this->_endPoint = "/_all";
		$this->sendGet();

		return $this->data();
	}

	public function get() {
		parent::sendGet();
		return $this->data();
	}
}

?>