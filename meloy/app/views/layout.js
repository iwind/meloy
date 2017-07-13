Tea.View.scope(function () {
	this.moduleHelperIndex = -1;

	this.loadLayout = function () {
		//快速回到顶部
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

		//小助手
		Tea.action("helpers")
			.post()
			.success(function (response) {
				this.moduleHelpers = response.data.helpers;
			});
	};
	this.loadLayout();

	this.goTop = function () {
		window.scrollTo(0, 0);
	};

	this.viewExpand = function () {
		var body = angular.element(document.body);
		if (!body.hasClass("expanded")) {
			document.cookie = "viewExpanded=1; expires=Thu, 18 Dec 2020 12:00:00; path=/"
			this.viewExpanded = true;
		}
		else {
			document.cookie = "viewExpanded=1; expires=Thu, 18 Dec 2000 12:00:00; path=/"
			this.viewExpanded = false;
		}
	};

	this.showModuleHelper = function (moduleHelper, index) {
		var viewBox = document.getElementById("helper-view");
		if (viewBox.getElementsByTagName("iframe").length > 0) {
			viewBox.innerHTML = "";

			if (this.moduleHelperIndex == index) {
				this.moduleHelperIndex = -1;
				return;
			}
		}

		this.moduleHelperIndex = index;
		var url = moduleHelper.url;
		if (url.indexOf("?") > -1) {
			url += "&_size=" + moduleHelper.size;
		}
		else {
			url += "?_size=" + moduleHelper.size;
		}
		url += "&_name=" + encodeURIComponent(moduleHelper.name);
		url += "&_developer=" + encodeURIComponent(moduleHelper.developer);
		url += "&_module=" + encodeURIComponent(moduleHelper.module);
		viewBox.innerHTML = '<iframe src="' + url + '" scrolling="no" allowtransparency="yes"></iframe>';
	};
});