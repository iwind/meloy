Tea.View.scope(function () {
	this.origin = "";
	this.result = "";

	this._init = function () {
		document.querySelector("textarea").focus();
	};

	this.changeOrigin = function () {
		this.changeFn();
	};

	this.changeFn = function () {
		Tea.action(".convert")
			.post()
			.params({
				"fn": this.fn,
				"origin": this.origin
			})
			.success(function (response) {
				this.result = response.data.result;
			});
	};

	this.exchange = function () {
		this.origin = this.result;
		this.changeOrigin();
	};
});