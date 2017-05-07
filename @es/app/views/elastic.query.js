function Query() {
	this.fieldTypes = {
		"long":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"integer":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"short":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"byte":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"double":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"float":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"text":  [ [ "term", "等于" ], [ "match", "匹配" ], [ "wildcard", "通配符" ], [ "prefix", "前缀" ], [ "fuzzy", "模糊查询" ] ],
		"keyword":  [ [ "term", "等于" ], [ "match", "匹配" ], [ "wildcard", "通配符" ], [ "prefix", "前缀" ], [ "fuzzy", "模糊查询" ] ],
		"date":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"boolean": [ [ "term", "等于" ] ]
		//"geo_point": [  ],
		//"geo_shape": []
		//"ip": []
		//"completion"
	};

	this.queryFields = []; // { field, types, type, value }

	this.supportsType = function (type) {
		return angular.isDefined(this.fieldTypes[type]);
	};

	this.addField = function (name, type) {
		this.queryFields.push({
			"field": name,
			"dataType": type,
			"types": this.fieldTypes[type]
		});
	};

	this.addQueryString = function () {
		this.queryFields.push({
			"field": "查询字符串(query_string)",
			"dataType": "spec:query_string",
			"types": [ [ "spec:query_string", "查询字符串" ] ],
			"type": "spec:query_string"
		});
	};

	this.addScript = function () {
		this.queryFields.push({
			"field": "脚本(script)",
			"dataType": "spec:script",
			"types": [ [ "spec:script", "脚本" ] ],
			"type": "spec:script"
		});
	};

	this.deleteField = function (index) {
		this.queryFields.$remove(index);
	};

	this.dsl = function () {
		var dsl = {
			"query": {}
		};

		this.queryFields.$each(function (k, v) {
			var realValue = (angular.isDefined(v.dataType) && angular.isDefined(v.value) && [ "long", "integer", "byte", "short", "float", "double" ].$contains(v.dataType)) ? parseInt(v.value, 10) : v.value;

			//特殊类型处理
			if (angular.isDefined(realValue)) {
				if (v.dataType == "boolean") {
					realValue = (realValue == "1");
				}
			}

			if (v.type == "term") {
				if (!angular.isDefined(dsl.query.term)) {
					dsl.query.term = {};
				}
				if (angular.isDefined(realValue)) {
					dsl.query.term[v.field] = realValue;
				}
			}
			else if (v.type == "match") {
				if (!angular.isDefined(dsl.query.match)) {
					dsl.query.match = {};
				}
				if (angular.isDefined(realValue)) {
					dsl.query.match[v.field] = realValue;
				}
			}
			else if (v.type == "wildcard") {
				if (!angular.isDefined(dsl.query.wildcard)) {
					dsl.query.wildcard = {};
				}
				if (angular.isDefined(realValue)) {
					dsl.query.wildcard[v.field] = realValue;
				}
			}
			else if (v.type == "prefix") {
				if (!angular.isDefined(dsl.query.prefix)) {
					dsl.query.prefix = {};
				}
				if (angular.isDefined(realValue)) {
					dsl.query.prefix[v.field] = realValue;
				}
			}
			else if (v.type == "fuzzy") {
				if (!angular.isDefined(dsl.query.fuzzy)) {
					dsl.query.fuzzy = {};
				}
				if (angular.isDefined(realValue)) {
					dsl.query.fuzzy[v.field] = realValue;
				}
			}
			else if ([ "gt", "gte", "lt", "lte" ].$contains(v.type)) {
				if (!angular.isDefined(dsl.query.range)) {
					dsl.query.range = {};
				}
				if (!angular.isDefined(dsl.query.range[v.field])) {
					dsl.query.range[v.field] = {};
				}
				if (angular.isDefined(realValue)) {
					dsl.query.range[v.field][v.type] = realValue;
				}
			}
			else if (v.type == "spec:query_string") {
				dsl.query.query_string = {
					"query": realValue
				};
			}
			else if (v.type == "spec:script") {
				if (angular.isString(realValue) && realValue.length > 0) {
					dsl.query.script = {
						"script": {
							"lang": "painless",
							"inline": realValue,
							"params": {}
						}
					};
				}
			}
		});

		return dsl;
	};
}