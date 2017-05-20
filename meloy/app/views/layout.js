Tea.View.scope(function () {
	this.loadLayout = function () {
		var btn = angular.element(document.querySelector(".go-top-btn"));
		angular.element(window).bind("scroll", function () {
			if (document.body.scrollTop > 0) {
				if (btn.hasClass("hidden")) {
					btn.removeClass("hidden");
				}
			}
			else {
				if (!btn.hasClass("hidden")) {
					btn.addClass("hidden");
				}
			}
		});
	};
	this.loadLayout();

	this.goTop = function () {
		window.scrollTo(0, 0);
	};
});