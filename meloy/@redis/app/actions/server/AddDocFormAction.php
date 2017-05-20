<?php

namespace redis\app\actions\server;

class AddDocFormAction extends BaseAction {
	public function run() {
		$this->data->types = [
			[
				"name" => "字符串",
				"code" => "string"
			],
			[
				"name" => "Hash",
				"code" => "hash"
			],
			[
				"name" => "列表",
				"code" => "list"
			],
			[
				"name" => "集合",
				"code" => "set"
			],
			[
				"name" => "排序集合",
				"code" => "zset"
			],
		];
	}
}

?>