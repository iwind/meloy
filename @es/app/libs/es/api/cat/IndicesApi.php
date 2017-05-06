<?php

namespace es\api\cat;

class IndicesApi extends CatApi {
	public function get() {
		$this->_endPoint = "/_cat/indices";

		$this->sendGet();
		return $this->data();
	}
}

?>