<?php

namespace app\classes;

use tea\Request;

class HelperAction extends AuthAction {

	public function before() {
		parent::before();

		$request = Request::shared();
		$this->data->_helper = (object)[
			"size" => $request->param("_size"),
			"name" => $request->param("_name"),
			"developer" => $request->param("_developer"),
			"module" => $request->param("_module")
		];
	}
}

?>