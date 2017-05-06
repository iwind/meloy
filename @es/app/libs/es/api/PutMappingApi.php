<?php

namespace es\api;

use es\Exception;
use es\Mapping;

class PutMappingApi extends Api {
	public function put(Mapping $mapping) {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}
		if (is_empty($this->type())) {
			throw new Exception("please specify type name");
		}
		$this->_endPoint = "/" . $this->index() . "/_mapping/" . $this->type();
		$this->payload($mapping->asJson());

		$this->sendPut();

		return $this->data();
	}
}

?>