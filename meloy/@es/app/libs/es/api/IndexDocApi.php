<?php

namespace es\api;

use es\Exception;

class IndexDocApi extends Api {
	public function put($id, $object) {
		if (is_empty($this->index())) {
			throw new Exception("Please specify index name");
		}

		if (is_empty($this->type())) {
			throw new Exception("Please specify type name");
		}

		if (is_empty($id)) {
			throw new Exception("Please specify id for the document");
		}

		$this->_endPoint = "/" . $this->index() . "/" . $this->type() . "/" . $id;

		$this->payload(json_encode($object));
		$this->sendPut();

		return $this->data();
	}
}

?>