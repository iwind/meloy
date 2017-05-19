Tea.View.scope(function () {
	this.newElements = [];
	this.updatingIndex = -1;
	this.newItemValue = null;

	this.newItemPosition = 1;
	this.newItemIndex = -1;

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

	this.deleteItem = function (index) {
		if (!window.confirm("确定要删除此元素吗，删除后数据不可恢复？位置:" + index)) {
			return;
		}

		Tea.action(".deleteListItem")
			.params({
				"serverId": this.server.id,
				"key": this.key,
				"index": index
			})
			.post()
			.success(function () {
				window.location.reload();
			});
	};

	this.updateItemForm = function (index, value) {
		if (this.updatingIndex == index) {
			this.updatingIndex = -1;
			return;
		}

		this.updatingIndex = index;
		this.newItemValue = value;

		setTimeout(function () {
			var box = document.getElementsByClassName("updating-box")[0];
			var textArea = box.querySelector("textarea");
			textArea.focus();
		}, 100);
	};

	this.updateItem = function (index) {
		Tea.action(".updateListItem")
			.params({
				"serverId": this.server.id,
				"key": this.key,
				"index": index,
				"value": this.newItemValue
			})
			.post()
			.success(function () {
				window.location.reload();
			});
	};

	this.cancelItemUpdating = function () {
		this.updatingIndex = -1;
	};

	this.changeNewItemPosition = function () {
		if (this.newItemPosition == 1) {
			this.newItemIndex = -1;
		}
		else if (this.newItemPosition == 2) {
			this.newItemIndex = 0;
		}
		else if (this.newItemPosition == 3) {
			this.newItemIndex = -1;
		}
	};
});