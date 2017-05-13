<?php

namespace es\api;

class NodesApi extends Api {
	public function getAll() {
		$this->_endPoint = "/_nodes";
		$this->sendGet();
		return $this->data()->nodes;
	}
}

?>