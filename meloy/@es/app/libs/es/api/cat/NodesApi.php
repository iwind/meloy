<?php

namespace es\api\cat;

class NodesApi extends CatApi {
	public function getAll() {
		$this->_endPoint = "/_cat/nodes";

		$this->sendGet();

		return $this->data();
	}
}

?>