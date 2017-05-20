Tea.View.scope(function () {
	var that = this;

	this.load = function () {
		setInterval(function () {
			that.now = (new Tea.Date()).parse("Y-m-d H:i:s");
			Tea.View.update();
		}, 1000)
	};

	this.load();
});