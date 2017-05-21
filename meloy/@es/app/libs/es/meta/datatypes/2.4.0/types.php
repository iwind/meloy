<?php

return [
	[
		"name" => "核心(Core datatypes)",
		"types" => [
			[ "字符串", [ "string" ] ],
			[ "数字类型", [ "long", "integer", "short", "byte", "double", "float" ] ],
			[ "日期时间", [ "date" ] ],
			[ "布尔值", [ "boolean" ] ],
			[ "二进制", [ "binary" ] ],
		]
	],
	[
		"name" => "地理位置(Geo datatypes)",
		"types" => [
			[ "点", [ "geo_point" ] ],
			[ "形状", [ "geo_shape" ] ]
		]
	],
	[
		"name" => "特殊类型(Specialised datatypes)",
		"types" => [
			[ "IP数据类型", [ "ip" ] ],
			[ "自动完成", [ "completion" ] ],
			[ "分词数量", [ "token_count" ] ]
		]
	]
];

?>