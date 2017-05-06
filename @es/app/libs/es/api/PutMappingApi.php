<?php

namespace es\api;

use es\Exception;
use es\Mapping;

class PutMappingApi extends Api {
	public function put($mapping) {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}
		if (is_empty($this->type())) {
			throw new Exception("please specify type name");
		}
		$this->_endPoint = "/" . $this->index() . "/_mapping/" . $this->type();

		if ($mapping instanceof Mapping) {
			$this->payload($mapping->asJson());
		}
		else {
			$this->payload($mapping);
		}

		$this->sendPut();

		return $this->data();
	}

	public function putAll($mappings) {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}
		$this->_endPoint = "/" . $this->index();

		$this->payload((object)[
			"mappings" => $mappings
		]);

		$this->sendPut();

		return $this->data();
	}
}

?>