Tea.View.scope(function () {
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

	this.showView = function (view) {
		this.view = view;
		window.location.hash = "#" + view;
	};
});