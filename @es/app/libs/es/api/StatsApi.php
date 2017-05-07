<?php

namespace es\api;

class StatsApi extends Api {
	public function get() {
		$this->_endPoint = "/_stats";

		if (!is_empty($this->index())) {
			$this->_endPoint = "/" . $this->index() . "/_stats";
		}

		$this->sendGet();

		return $this->data();
	}
}

?>