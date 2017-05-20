Tea.View.scope(function () {
	this.newElements = [];
	this.updatingItem = null;
	this.newItem = {
		"value": null
	};

	this.ttl = "-1";
	this.view = "update";
	var that = this;

	this.load = function () {
		//TTL显示当前时间
		setInterval(function () {
			if (that.ttl == 0 && that.view == "ttl") {
				that.now = (new Tea.Date()).parse("Y-m-d H:i:s");
				Tea.View.update();
			}
		}, 1000);

		//选中标签
		var hash = window.location.hash.substr(1);
		if (hash.length > 0) {
			that.showView(hash);
			Tea.View.update();
		}
	};

	setTimeout(function () {
		that.load();
	});

	this.deleteDoc = function (key) {
		if (!window.confirm("确定要删除此数据吗？")) {
			return;
		}

		Tea.action(".delete")
			.params({
				"key": key,
				"serverId": this.server.id
			})
			.post()
			.success(function () {
				window.location = this.g;
			});
	};

	this.deleteItem = function (itemValue) {
		if (!window.confirm("确定要删除此元素吗，删除后数据不可恢复？元素:" + itemValue)) {
			return;
		}

		Tea.action(".deleteSetItem")
			.params({
				"serverId": this.server.id,
				"key": this.key,
				"item": itemValue
			})
			.post()
			.success(function () {
				window.location.reload();
			});
	};

	this.updateItemForm = function (itemValue) {
		if (this.updatingItem == itemValue) {
			this.updatingItem = null;
			return;
		}

		this.updatingItem = itemValue;
		this.newItem.value = itemValue;

		setTimeout(function () {
			var box = document.getElementsByClassName("updating-box")[0];
			var textArea = box.querySelector("textarea");
			textArea.focus();
		}, 100);
	};

	this.updateItem = function (itemValue) {
		Tea.action(".updateSetItem")
			.params({
				"serverId": this.server.id,
				"key": this.key,
				"item": itemValue,
				"value": this.newItem.value
			})
			.post()
			.success(function () {
				window.location.reload();
			});
	};

	this.cancelItemUpdating = function () {
		this.updatingItem = null;
	};

	this.showView = function (view) {
		this.view = view;
		window.location.hash = "#" + view;
	};
});