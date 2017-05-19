Tea.View.scope(function () {
	this.newElements = [];
	this.updatingItem = null;
	this.newItem = {
		"value": null
	};

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
});