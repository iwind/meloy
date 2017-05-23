Tea.View.scope(function () {
	this.dataJson = {};
	this.docId = "";
	this.endPoint = "PUT /" + this.index.name + "/" + this.type.name + "/[_id]";

	var that = this;

	this.load = function () {
		var form = document.getElementById("addDocForm");
		Array.from(form.querySelectorAll("input")).$each(function (k, input) {
			angular.element(input).bind("input", function () {
				that.convertJson();
			});
		});
		Array.from(form.querySelectorAll("select")).$each(function (k, input) {
			angular.element(input).bind("change", function () {
				that.convertJson();
			});
		});
		Array.from(form.querySelectorAll("textarea")).$each(function (k, input) {
			angular.element(input).bind("input", function () {
				that.convertJson();
			});
		});
	};

	setTimeout(function () {
		that.load();
		that.convertJson();

		var form = document.getElementById("addDocForm");
		angular.element(form).bind("DOMSubtreeModified", function () {
			that.load();
		});
	}, 0);

	that.$watch("docId", function (v1) {
		if (typeof(v1) != "string" || v1.length == 0) {
			v1 = "[_id]";
		}
		that.endPoint = "PUT /" + that.index.name + "/" + that.type.name + "/" + v1;
	});

	this.convertJson = function () {
		var form = document.getElementById("addDocForm");

		Tea.action("@.doc.jsonData")
			.params(new FormData(form))
			.post()
			.success(function (response) {
				that.dataJson = response.data.values;
				setTimeout(function () {
					hljs.highlightBlock(document.querySelector("pre.source-code"))
				}, 50);
			});
	};
});