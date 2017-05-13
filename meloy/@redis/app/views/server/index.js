Tea.View.scope(function () {
	var that = this;

	this.load = function () {
		var box = document.getElementById("docs-box");
		if (box) {
			var docElements = box.getElementsByClassName("doc");
			for (var i = 0; i < docElements.length; i++) {
				hljs.highlightBlock(docElements[i]);
			}
		}
	};

	that.load();

	this.deleteItem = function (item) {
		if (!window.confirm("确定要删除此数据吗？")) {
			return;
		}

		Tea.action(".deleteItem")
			.params({
				"key": item.key,
				"serverId": this.server.id
			})
			.post()
			.success(function () {
				window.location.reload();
			});
	};

	this.clearQ = function () {
		this.q = null;
	};

	this.searchKeyword = function () {
		Tea.go(".index", {
			"serverId": this.server.id,
			"q": this.q
		});
	};
});