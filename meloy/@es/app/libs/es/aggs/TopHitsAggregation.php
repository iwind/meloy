<?php

namespace es\aggs;

use es\scripts\Script;

class TopHitsAggregation extends Aggregation {
	private $_from;
	private $_size;
	private $_sort = [];
	private $_includes = [];
	private $_scriptFields = [];

	public function setFrom($from) {
		$this->_from = $from;
		return $this;
	}

	public function setSize($size) {
		$this->_size = $size;
		return $this;
	}

	public function asc($field) {
		if ($field instanceof Script) {
			$this->_sort["_script"] = [
				"type" => "number",
				"script" => $field->asArray(),
				"order" => "asc"
			];
		}
		else {
			$this->_sort[$field] = [
				"order" => "asc"
			];
		}
		return $this;
	}

	public function desc($field) {
		if ($field instanceof Script) {
			$this->_sort["_script"] = [
				"type" => "number",
				"script" => $field->asArray(),
				"order" => "desc"
			];
		}
		else {
			$this->_sort[$field] = [
				"order" => "desc"
			];
		}
		return $this;
	}

	public function sortNear($lat, $lng) {
		$this->_sort["_geo_distance"] = [
			"location" => [ "lat" => $lat, "lon" => $lng ],
			"order" => "asc",
			"unit" => "m"
		];
		return $this;
	}

	public function result(... $fields) {
		foreach ($fields as $field) {
			if (is_array($field)) {
				call_user_func_array([ $this, "result" ], $field);
			}
			else {
				if ($field instanceof Script) {
					$this->_scriptFields[$field->name()] = [
						"script" => $field->asArray()
					];
					$this->_includes[] = $field->name();
				}
				else {
					$this->_includes[] = $field;
				}
			}
		}
		return $this;
	}

	public function asArray() {
		$arr = [];
		if (!is_null($this->_from)) {
			$arr["from"] = $this->_from;
		}
		if (!is_null($this->_size)) {
			$arr["size"] = $this->_size;
		}
		if (!empty($this->_sort)) {
			$arr["sort"] = $this->_sort;
		}

		if (!empty($this->_includes)) {
			$arr["_source"] = [
				"includes" => $this->_includes
			];
		}
		if (!empty($this->_scriptFields)) {
			$arr["script_fields"] = $this->_scriptFields;
		}

		return [
			"top_hits" => empty($arr) ? (object)[] : $arr,
		];
	}
}

?>