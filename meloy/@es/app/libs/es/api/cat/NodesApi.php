<?php

namespace es\api\cat;

class NodesApi extends CatApi {
	public function get() {
		$this->_endPoint = "/_cat/nodes";

		$this->sendGet();

		return $this->data();
	}
}

?>