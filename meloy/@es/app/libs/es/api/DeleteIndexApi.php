<?php

namespace es\api;

use es\Exception;

class DeleteIndexApi extends Api {
	public function delete() {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name to delete");
		}
		$this->_endPoint = "/" . $this->index();
		$this->sendDelete();
		return $this->data();
	}
}

?>