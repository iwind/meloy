<?php

namespace es\api;

use es\Exception;

class IndicesExistApi extends Api {
	public function exist() {
		$this->_endPoint = "/" . $this->index();

		try {
			$this->sendHead();
		} catch (Exception $e) {

		}
		return $this->code() == 200;
	}
}

?>