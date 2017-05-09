<?php

namespace es\api;

use es\Exception;

class CreateIndexApi extends Api {
	public function create() {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name to create");
		}
		$this->_endPoint = "/" . $this->index();
		$this->sendPut();
	}
}

?>