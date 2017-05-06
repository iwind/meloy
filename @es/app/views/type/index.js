Tea.View.scope(function () {
	this.load = function () {
		var box = document.getElementById("docs-box");
		if (box) {
			var docElements = box.getElementsByClassName("doc");
			for (var i = 0; i < docElements.length; i++) {
				hljs.highlightBlock(docElements[i]);
			}
		}
	};

	this.load();

	this.switchViewport = function (index) {
		var box = document.getElementById("docs-box");
		var docElements = box.getElementsByClassName("doc");
		var orginElement = docElements[index];
		var element = angular.element(orginElement);
		if (element.hasClass("full")) {
			element.removeClass("full");
		}
		else {
			element.addClass("full");
			element.prop("scrollTop", 5);
		}
	};
});
