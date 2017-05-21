<?php

namespace es\api;

use es\Exception;

class IndexAliasesApi extends Api {
	public function add($alias, $routing = null, $searchRouting = null, $indexRouting = null, $filter = null) {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}

		$this->_endPoint = "/_aliases";

		$params = [
			"index" => $this->index(),
			"alias" => $alias
		];

		if (!is_empty($routing)) {
			$params["routing"] = $routing;
		}

		if (!is_empty($searchRouting)) {
			$params["search_routing"] = $searchRouting;
		}

		if (!is_empty($indexRouting)) {
			$params["index_routing"] = $indexRouting;
		}

		if (!is_empty($filter)) {
			$params["filter"] = $filter;
		}

		$this->payload(json_encode([
			"actions" => [
				[ "add" => $params ]
			]
		]));

		$this->sendPost();

		return $this->data();
	}

	public function remove($alias) {
		if (is_empty($this->index())) {
			throw new Exception("please specify index name");
		}

		$this->_endPoint = "/_aliases";
		$this->payload(json_encode([
			"actions" => [
				[
					"remove" => [
						"index" => $this->index(),
						"alias" => $alias
					]
				]
			]
		]));

		$this->sendPost();

		return $this->data();
	}
}

?>