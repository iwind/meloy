<?php

namespace es\api;

class GetIndexApi extends Api {
	public function getAll() {
		$this->_endPoint = "/_all";
		$this->sendGet();

		return $this->data();
	}

	public function get() {
		$index = $this->index();
		if (!is_empty($index)) {
			$this->_endPoint = "/" . $index;
		}

		parent::sendGet();

		$data = $this->data();

		if (is_empty($index)) {
			return $data;
		}
		return $data->$index;
	}
}

?>