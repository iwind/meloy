Tea.View.scope(function () {
	this.load = function () {
		var that = this;
		setInterval(function () {
			that.now = (new Tea.Date()).parse("Y-m-d H:i:s");
			Tea.View.update();
		}, 1000)
	};

	this.load();

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