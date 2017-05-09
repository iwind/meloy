<?php

namespace es\api;

class ReindexApi extends Api {
	private $_sourceIndex;
	private $_destIndex;
	private $_types = [];
	private $_waitForCompletion = true;

	public function types(array $types) {
		$this->_types = $types;
		return $this;
	}

	public function sourceIndex($sourceIndex) {
		$this->_sourceIndex = $sourceIndex;
		return $this;
	}

	public function destIndex($destIndex) {
		$this->_destIndex = $destIndex;
		return $this;
	}

	public function waitForCompletion($bool = true) {
		$this->_waitForCompletion = $bool;
		return $this;
	}

	public function exec() {
		$payload = array(
			"source" => [
				"index" => $this->_sourceIndex
			],
			"dest" => [
				"index" => $this->_destIndex
			]
		);

		if (!empty($this->_types)) {
			$payload["source"]["type"] = array_values($this->_types);
		}

		$this->param("wait_for_completion", $this->_waitForCompletion ? "true" : "false");

		$this->_endPoint = "/_reindex";
		$this->payload(json_encode($payload));
		$this->sendPost();

		return $this->data();
	}
}

?>