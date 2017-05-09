<?php

namespace es\api;

use es\Exception;

class GetMappingApi extends Api {
	public function get() {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}

		if (is_empty($this->type())) {
			throw new Exception("please specify type name");
		}

		$this->_endPoint = "/" . $this->index() . "/_mapping/" . $this->type();

		$this->sendGet();

		return $this->dataValue($this->index() . ".mappings." . $this->type());
	}

	public function getAll() {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}

		$this->_endPoint = "/" . $this->index() . "/_mapping";

		$this->sendGet();

		return $this->dataValue($this->index() . ".mappings");
	}
}

?>