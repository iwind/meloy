<?php

namespace es\queries;

abstract class Query {
	public function __construct() {

	}

	/**
	 * @return static
	 */
	public static function create() {
		return new static;
	}

	public function asJson() {
		return json_encode($this->asArray());
	}

	public function asPrettyJson() {
		return json_encode($this->asArray(), JSON_PRETTY_PRINT);
	}

	public abstract function name();
	public abstract function asArray();
}

?>