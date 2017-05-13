<?php

namespace es\api\cat;

class PluginsApi extends CatApi {
	public function getAll() {
		$this->_endPoint = "/_cat/plugins";

		$this->sendGet();

		return $this->data();
	}
}

?>