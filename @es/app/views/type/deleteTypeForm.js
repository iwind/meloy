Tea.View.scope(function () {
	this.beforeDelete = function (form) {
		angular.element(form).addClass("loading");
	};
});