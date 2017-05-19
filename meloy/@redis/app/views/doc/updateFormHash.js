Tea.View.scope(function () {
	this.newElements = [];

	this.removeItem = function (key) {
		if (typeof(this.value) == "object") {
			delete this.value[key];
		}
	};

	this.countValidItems = function () {
		var count = 0;
		for (var key in this.value) {
			if (this.value.hasOwnProperty(key)) {
				count ++;
			}
		}
		return count;
	};

	this.addElement = function () {
		this.newElements.push(Math.random());
	};

	this.removeElement = function (index) {
		this.newElements.$remove(index);
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
});