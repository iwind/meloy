Tea.View.scope(function () {
	this.selectServer = function (server) {
		Tea.go(server.module + ".server", { "serverId": server.id });
	};
});