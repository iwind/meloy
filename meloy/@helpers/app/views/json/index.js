Tea.View.scope(function () {
	this.jsonData = "";

	this.format = function () {
		var object = {
			"name": "Hello"
		};
		try {
			eval("object=" + this.jsonData);
			this.jsonData = angular.toJson(object, 2);
		} catch (error) {
			alert(error.toString());
		}
	};
});