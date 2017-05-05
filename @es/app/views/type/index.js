Tea.View.scope(function () {
	var box = document.getElementById("docs-box");
	var docElements = box.getElementsByClassName("doc");
	for (var i  = 0; i < docElements.length; i ++) {
		hljs.highlightBlock(docElements[i]);
	}
});
