<?php

namespace es\api;

class SearchApi extends Api {
	public function search() {
		if (is_empty($this->index())) {
			$this->_endPoint = "/_search";
		}
		else if (is_empty($this->type())) {
			$this->_endPoint = "/" . $this->index() . "/_search";
		}
		else {
			$this->_endPoint = "/" . $this->index() . "/" . $this->type() . "/_search";
		}

		$this->sendGet();
		return $this->data();
	}
}

?>