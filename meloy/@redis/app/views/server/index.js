Tea.View.scope(function () {
	this.deleteDoc = function (doc) {
		if (!window.confirm("确定要删除此数据吗？")) {
			return;
		}

		Tea.action("@.doc.delete")
			.params({
				"key": doc.key,
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

	this.g = function () {
		return window.location.toString();
	};
});