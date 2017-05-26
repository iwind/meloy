Tea.View.scope(function () {
	this.reloadHelperView = function () {
		console.log("reloadHelperView");
		var helperBox = document.getElementById("helper-box");
		var width = helperBox.offsetWidth + 20;
		var height = helperBox.offsetHeight + 20;
		var helperView = window.parent.document.getElementById("helper-view");
		if (helperView != null) {
			helperView.style.width = width + "px";
			helperView.style.height = height + "px";
		}
	};

	Tea.delay(function () {
		this.reloadHelperView();
	});

	this.closeHelperWindow = function () {
		var helperView = window.parent.document.getElementById("helper-view");
		if (helperView != null) {
			helperView.innerHTML = "";
		}
		else {
			alert("此功能在小助手嵌入到页面时有效");
		}
	};
});