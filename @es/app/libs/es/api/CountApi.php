<?php

namespace es\api;

class CountApi extends Api {
	public function count() {
		if (is_empty($this->index())) {
			$this->_endPoint = "/_count";
		}
		else if (is_empty($this->type())) {
			$this->_endPoint = "/" . $this->index() . "/_count";
		}
		else {
			$this->_endPoint = "/" . $this->index() . "/" . $this->type() . "/_count";
		}

		$this->sendGet();

		$data = $this->data();
		if (is_object($data) && isset($data->count)) {
			return $data->count;
		}
		return 0;
	}
}

?>