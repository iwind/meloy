Tea.View.scope(function () {
	this.load = function () {
		var box = document.getElementById("docs-box");
		if (box) {
			var docElements = box.getElementsByClassName("doc");
			for (var i = 0; i < docElements.length; i++) {
				hljs.highlightBlock(docElements[i]);
			}
		}
	};

	this.load();

	this.openViewPort = function (doc, index) {
		if (doc.isOpen) {
			return;
		}

		var box = document.getElementById("docs-box");
		var docElements = box.getElementsByClassName("doc");
		var element = docElements[index];
		var beforeHeight = element.offsetHeight;

		doc.isOpen = true;

		setTimeout(function () {
			var afterHeight = element.offsetHeight;
			if (beforeHeight == afterHeight) {
				doc.isOpen = false;
				Tea.View.update();
			}
		}, 10);
	};

	this.closeViewPort = function (doc) {
		doc.isOpen = false;
	};

	this.searchKeyword = function () {
		Tea.go(".index", {
			"serverId": this.server.id,
			"index": this.index.name,
			"type": this.type.name,
			"search": "q",
			"q": this.q
		});
		return false;
	};

	this.clearQ = function () {
		this.q = "";
	};

	this.deleteDoc = function (doc) {
		if (!window.confirm("确定要删除此文档吗？")) {
			return;
		}

		doc.loading = true;
		doc.loadingText = "删除中";

		Tea.action(".deleteDoc")
			.params({
				"serverId": this.server.id,
				"index": this.index.name,
				"type": this.type.name,
				"id": doc._id
			})
			.post()
			.delay(1);
	};
});
