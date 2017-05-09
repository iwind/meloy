<?php

namespace es\api\cat;

use es\api\Api;

class CatApi extends Api {
	public function __construct() {
		parent::__construct();

		$this->param("format", "json");
	}
}

?>