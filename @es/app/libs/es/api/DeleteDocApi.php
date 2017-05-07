<?php

namespace es\api;

use es\Exception;

class DeleteDocApi extends Api {
	public function delete($id) {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}

		if (is_empty($this->type())) {
			throw new Exception("please specify type name");
		}

		$this->_endPoint = "/" . $this->index() . "/" . $this->type() . "/" . $id;

		$this->sendDelete();

		return $this->data();
	}
}

?>