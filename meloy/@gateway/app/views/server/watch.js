Tea.View.scope(function () {
	this.selectedLog = null;
	this.logs = [];
	var allLogs = [];

	this.apiKeyword = "";

	this.refresh = function () {
		var scope = this;
		Tea.action(".watchData")
			.params({
				"serverId": scope.server.id
			})
			.post()
			.success(function (response) {
				var lastLogId = (allLogs.length > 0) ? allLogs.$first().id : 0;
				response.data.logs.$sort(function (log1, log2) {
					return log1.id - log2.id;
				});
				response.data.logs.$each(function (_, log) {
					if (log.id > lastLogId) {
						allLogs.$insert(0, log);
					}
				});

				this.logs = allLogs;
				this.searchApi(this.apiKeyword);

				//选中第一个
				if (this.selectedLog == null && this.logs.length > 0) {
					this.selectLog(this.logs[0]);
				}

				setTimeout(function () {
					scope.refresh();
				}, 3000);
			})
			.fail(function () {
				setTimeout(function () {
					scope.refresh();
				}, 3000);
			});
	};

	this.refresh();

	this.selectLog = function (log) {
		this.selectedLog = log;
	};

	this.searchApi = function (keyword) {
		console.log(keyword);
		if (keyword == null || keyword.length == 0) {
			this.logs = allLogs;
			return;
		}

		var regexps = [];
		for (var i  = 0; i < keyword.length; i ++) {
			var char = keyword.charAt(i);
			if (!char.match(/\s/)) {
				regexps.push(new RegExp(char, "i"));
			}
		}

		function matchAll(s) {
			return regexps.$all(function(_, v) {
				return v.test(s);
			});
		}

		this.logs = allLogs.$filter(function (k, v) {
			return matchAll(v.request.uri);
		});
	};

	this.clearSearchKeyword = function () {
		this.apiKeyword = "";
		this.logs = allLogs;
	};
});