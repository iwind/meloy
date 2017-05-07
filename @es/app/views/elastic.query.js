function Query() {
	this.fieldTypes = {
		"long":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"integer":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"short":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"byte":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"double":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"float":  [ [ "term", "等于" ], [ "gt", "大于" ], [ "lt", "小于" ], [ "gte", "大于等于" ],  [ "lte", "小于等于" ]],
		"text":  [ [ "term", "等于" ], [ "match", "匹配" ] ],
		"keyword":  [ [ "term", "等于" ], [ "match", "匹配" ]],
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

	this.deleteField = function (index) {
		this.queryFields.$remove(index);
	};

	this.dsl = function () {
		var dsl = {
			"query": {}
		};

		this.queryFields.$each(function (k, v) {
			var realValue = (angular.isDefined(v.dataType) && angular.isDefined(v.value) && [ "long", "integer", "byte", "short", "float", "double" ].$contains(v.dataType)) ? parseInt(v.value, 10) : v.value;

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
		});

		return dsl;
	};
}