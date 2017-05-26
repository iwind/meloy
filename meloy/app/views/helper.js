Tea.View.scope(function () {
	this.reloadHelperView = function () {
		var helperBox = document.getElementById("helper-box");
		var width = helperBox.offsetWidth + 20;
		var height = helperBox.offsetHeight + 20;
		var helperView = window.parent.document.getElementById("helper-view");
		helperView.style.width = width + "px";
		helperView.style.height = height + "px";
	};

	Tea.delay(function () {
		this.reloadHelperView();
	});
});