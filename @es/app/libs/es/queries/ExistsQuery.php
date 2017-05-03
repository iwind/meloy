<?php

namespace es\queries;

class ExistsQuery extends TermQuery {

	public function name() {
		return "exists";
	}

	public function asArray() {
		return [
			 "field" => $this->field()
		];
	}
}

?>