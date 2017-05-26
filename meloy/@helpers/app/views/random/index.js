Tea.View.scope(function () {
	this.length = 32;
	this.containsNumbers = true;
	this.containsLowercase = true;
	this.containsUppercase = true;
	this.containsPunctuation = false;
	this.result = "";

	this.load = function () {
		this.rand();
	};

	Tea.delay(function () {
		this.load();
	});

	this.rand = function () {
		Tea.action(".rand")
			.params({
				"length": this.length,
				"containsNumbers": this.containsNumbers ? 1 : 0,
				"containsLowercase": this.containsLowercase ? 1 : 0,
				"containsUppercase": this.containsUppercase ? 1 : 0,
				"containsPunctuation": this.containsPunctuation ? 1 : 0
			})
			.post()
			.success(function (response) {
				this.result = response.data.result;
			});
	};
});