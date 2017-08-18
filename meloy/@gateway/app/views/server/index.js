Tea.View.scope(function () {
	var scope = this;

	var allApis = this.apis;
	if (typeof(allApis) == "undefined") {
		return;
	}

	// 操作
	this.selectedOperation = "chart";
	this.api.availableAddresses.$unique(function (_, v) {
		return v.url;
	});
	this.request = {
		"host": "0",
		"method": (this.api.methods.length > 0) ? this.api.methods[0] : "",
		"headers": [],
		"body": "",
		"query": "",
		"response": "",
		"costMs": 0,

		"requests": "5000",
		"concurrency": "100"
	};

	Tea.delay(function () {
		var chart = echarts.init(document.getElementById("stat-chart"));
		var avgRequests = [];
		var countMinutes = 0;
		var totalRequests = 0;
		var totalHits = 0;
		var totalErrors = 0;

		var avgMs = [];
		var totalMs = 0;
		var avgHits = [];
		var avgErrors = [];

		var hours = [];

		if (scope.stat.minutes) {
			scope.stat.minutes.$each(function (k, v) {
				countMinutes ++;
				totalRequests += v.requests;
				avgRequests.push(v.requests);

				totalHits += v.hits;
				if (v.hits > 0) {
					avgHits.push(v.hits);
				}
				else {
					avgHits.push(0)
				}

				if (v.errors > 0) {
					avgErrors.push(v.errors);
				}
				else {
					avgErrors.push(null);
				}

				totalMs += v.avgMs;
				avgMs.push(parseInt(totalMs / countMinutes));

				if (!hours.$contains(v.hour)) {
					hours.push(v.hour);
				}
				else {
					hours.push(null);
				}
			});
		}

		var option = {
			title: {
				text: "今日统计 (" + scope.stat.avgMs + " ms/req " + scope.stat.requests + " requests " +  scope.stat.hits + " hits " + scope.stat.errors + " errors)",
				textStyle: {
					fontSize: 14,
					fontWeight: "normal",
					color: "#666"
				},
				top: 0,
				x: "center"
			},
			tooltip : {
				trigger: 'axis',
				axisPointer: {
					type: 'cross',
					label: {
						backgroundColor: '#6a7985'
					}
				}
			},
			legend: {
				data:[ "请求次数(reqs/min)", "缓存命中次数(hits/min)", "错误次数(errors/min)" ],
				x: "center",
				y: "bottom"
			},
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '20%',
				left: '0%',
				right: '2%',
				bottom: '10%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data : hours,
					splitLine: {
						show: false
					},
					axisTick: {
						show: false
					},
					axisLabel: {
						show: false
					}
				}
			],
			yAxis : [
				{
					name: "次数",
					type: "value",
					max: null,
					splitLine: {
						show: false
					}
				}
			],
			series : [
				{
					name: "请求次数(reqs/min)",
					type:'line',
					smooth: true,
					animation: false,
					stack: '总量',
					/**lineStyle: {
						normal: {
							color: "#2f4554"
						}
					},**/
					itemStyle: {
						normal: {
							color: "#61a0a8"
						}
					},
					areaStyle: {
						normal: {
							"color": "#61a0a8"
						}
					},
					/**areaStyle: {normal: {
						"color": "green"
					}},**/
					showSymbol: false,
					data:avgRequests
				},
				{
					name: "缓存命中次数(hits/min)",
					type:'line',
					smooth: true,
					animation: false,
					yAxisIndex:0,
					stack: '总量2',
					symbol: 'none',
					/**lineStyle: {
						normal: {
							color: "rgb(255, 70, 131)"
						}
					},**/
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					areaStyle: {
						normal: {
							"color": "#749f83"
						}
					},
					showSymbol: false,
					data: avgHits
				},
				{
					name: "错误次数(errors/min)",
					type:'line',
					smooth: true,
					animation: false,
					yAxisIndex:0,
					stack: '总量3',
					symbol: 'none',
					/**lineStyle: {
						normal: {
							color: "rgb(255, 70, 131)"
						}
					},**/
					itemStyle: {
						normal: {
							color: "#c23531"
						}
					},
					areaStyle: {
						normal: {
							"color": "#c23531"
						}
					},
					showSymbol: false,
					data: avgErrors
				}
			]
		};

		chart.setOption(option);
	});

	this.showOperation = function (operation) {
		this.selectedOperation = operation;
	};

	this.refreshDebugLogs = function (path) {
		this.debugLogs = [];

		Tea.action(".debugLogs")
			.post()
			.params({
				"serverId": this.server.id,
				"path": path
			})
			.success(function (response) {
				this.debugLogs = response.data.debugLogs;

				setTimeout(function () {
					angular.element(document.body).prop("scrollTop", 100000);
				}, 100);
			});
	};

	/**
	 * 刷新API
	 */
	this.refreshApi = function () {
		window.location.reload();
	};

	this.searchApi = function (keyword) {
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

		this.apis = allApis.$filter(function (k, v) {
			return matchAll(v.path) || matchAll(v.name);
		});
	};

	this.clearSearchKeyword = function () {
		this.apiKeyword = "";
		this.apis = allApis;
	};

	/**
	 * 请求API
	 */
	this.requestApi = function () {
		this.benchmarkResult = null;

		var serverId = this.server.id;
		Tea.action(".request")
			.params({
				"serverId": serverId,

				"path": this.api.path,
				"host": this.request.host,
				"method": this.request.method,
				"headers": JSON.stringify(this.request.headers),
				"body": this.request.body,
				"query": this.request.query
			})
			.post()
			.success(function (response) {
				if (typeof(response.data.body) == "object") {
					this.request.response = response.data.body.response;
					this.request.costMs = Math.ceil(response.data.body.costMs * 100) / 100;
				}
				else {
					this.request.response = "[请求失败]";
					this.request.costMs = 0;
				}
			});
	};

	/**
	 * 基准测试
	 */
	this.benchmarkApi = function () {
		var serverId = this.server.id;
		this.benchmarkTesting = true;
		Tea.action(".benchmark")
			.params({
				"serverId": serverId,

				"path": this.api.path,
				"host": this.request.host,
				"method": this.request.method,
				"headers": JSON.stringify(this.request.headers),
				"body": this.request.body,
				"query": this.request.query,
				"requests": this.request.requests,
				"concurrency": this.request.concurrency
			})
			.timeout(60)
			.post()
			.success(function (response) {
				this.benchmarkTesting = false;

				if (response.data.result == null) {
					alert("请求失败");
					this.benchmarkResult = null;
				}
				else {
					this.benchmarkResult = response.data.result.result;
				}
			})
			.fail(function () {
				this.benchmarkResult = null;
				this.benchmarkTesting = false;
			})
			.error(function () {
				this.benchmarkResult = null;
				this.benchmarkTesting = false;
			});
	};

	// 滚动时调整API列表位置
	angular.element(window).bind("scroll", function () {
		var scrollTop = angular.element(document.body).prop("scrollTop");
		var element = angular.element(document.querySelector(".menu.api-list"));
		if (scrollTop == 0) {
			element.removeClass("top");
		}
		else {
			element.addClass("top");
		}
	});
});