Tea.View.scope(function () {
	this.servers = [];
	this.selectedServerIds = [];
	this.selectedServerId = 0;
	this.selectedDb = "";
	this.selectedTable = "";

	this.dbs = [];
	this.tables = [];

	this.loadServers = function () {
		Tea.post(".permissionServers", {
			"module": this.selectedModule
		}, function (response) {
			this.servers = response.data.servers;
		});
	};

	this.loadServers();

	this.selectAllServers = function () {
		if (this.selectedServerIds.length == this.servers.length) {
			this.selectedServerIds = [];
		}
		else {
			this.selectedServerIds = this.servers.$map(function (k, v) {
				return v.id;
			});
		}
	};

	this.selectServer = function (server) {
		if (this.selectedServerIds.$contains(server.id)) {
			this.selectedServerIds.$removeValue(server.id);
		}
		else {
			this.selectedServerIds.$push(server.id);
		}
	};

	this.containsServer = function (serverId) {
		return this.selectedServerIds.$contains(serverId);
	};

	this.showDbs = function (serverId) {
		Tea.post(".permissionDbs", {
			"serverId": serverId,
			"module": this.selectedModule
		}, function (response) {
			this.selectedServerId = serverId;
			this.dbs = response.data.dbs;
			this.serverOperations = response.data.operations;
			this.dbTypeName = response.data.dbTypeName;
		}, function (response) {
			this.selectedServerId = 0;
			this.dbs = [];
			this.serverOperations = [];

			alert(response.message);
		});
	};
});