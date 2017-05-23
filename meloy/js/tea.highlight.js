Tea.View.scope(function () {
	this.loadHighlights = function () {
		var docElements = document.querySelectorAll("pre.code");
		for (var i = 0; i < docElements.length; i++) {
			hljs.highlightBlock(docElements[i]);
		}
	};

	this.loadHighlights();
});