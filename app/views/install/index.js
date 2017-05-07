Tea.View.scope(function (){
	this.next = function () {
		Tea.go(".db");
	};

	this.refresh = function () {
		window.location.reload();
	};
});