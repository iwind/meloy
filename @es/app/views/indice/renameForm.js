Tea.View.scope(function () {
	this.loading = false;

	this.before = function () {
		this.loading = true;
	};

	this.fail = function () {
		this.loading = false;
	};
});