Tea.View.scope(function () {
	this.query = new Query();

	this.load = function () {
		this.highlight();
	};

	this.highlight = function () {
		setTimeout(function () {
			var docElements = document.getElementsByClassName("source-code");
			for (var i = 0; i < docElements.length; i++) {
				hljs.highlightBlock(docElements[i]);
			}
		}, 100);
	};

	this.queryJson = function () {
		this.highlight();
		return angular.toJson(this.query.dsl(), true);
	};

	this.search = function () {
		var json = angular.toJson(this.query.dsl());

		Tea.go(".index", {
			"serverId": this.server.id,
			"index": this.index.name,
			"type": this.type.name,
			"dsl": json
		});

		return false;
	};

	this.load();
});