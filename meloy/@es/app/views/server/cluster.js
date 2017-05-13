Tea.View.scope(function () {
	this.reload = function () {
		this.nodes = [];

		Tea.action(".cluster")
			.params({ "serverId": this.server.id })
			.post()
			.success(function (response) {
				this.nodes = response.data.nodes;
				Tea.View.update();
			});
	};
});