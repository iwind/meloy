<?php

namespace es\app\actions\server;

class IndexesAction extends BaseAction {
	public function run() {
		//@TODO 捕获异常

		$indexes = $this->_api->get("/_cat/indices?format=json", "");

		if (!empty($indexes)) {
			$titles = array_keys($indexes[0]);

			$indexAt = array_keys($titles, "index")[0];
			array_unshift($titles, "index");
			unset($titles[$indexAt + 1]);

			$indexes = array_map(function ($v) use ($indexAt) {
				$values =  array_values($v);
				array_unshift($values, $values[$indexAt]);
				unset($values[$indexAt + 1]);
				return $values;
			}, $indexes);

			$this->data->titles = $titles;
			$this->data->indexes = $indexes;
		}
	}
}

?>