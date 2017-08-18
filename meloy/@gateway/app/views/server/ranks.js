Tea.View.scope(function () {
	this.chartType = "chart";

	this.selectType = function (type) {
		this.chartType = type;
	};

	this.renderRequestsCharts = function () {
		var chart = echarts.init(document.getElementById("requests-chart-box"));
		var scope = this;
		var option = {
			title: {
				text: "请求数排行",
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
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '20%',
				left: '6%',
				right: '6%',
				bottom: '10%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					data : scope.requests.$map(function (k, v) {
						return v.path;
					}),
					splitLine: {
						show: false
					},
					axisLabel: {
						interval: 0,
						rotate: 30
					}
				}
			],
			yAxis : [
				{
					name: "请求次数",
					type: "value",
					max: null,
					splitLine: {
						show: false
					}
				}
			],
			series : [
				{
					name: "请求次数",
					type:'bar',
					barWidth: "30px",
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					data:scope.requests.$map(function (k, v) {
						return v.count;
					})
				}
			]
		};

		chart.setOption(option);
	};

	this.renderHitsCharts = function () {
		var chart = echarts.init(document.getElementById("hits-chart-box"));
		var scope = this;
		var option = {
			title: {
				text: "缓存命中率排行",
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
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '20%',
				left: '6%',
				right: '6%',
				bottom: '10%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					data : scope.hits.$map(function (k, v) {
						return v.path;
					}),
					splitLine: {
						show: false
					},
					axisLabel: {
						interval: 0,
						rotate: 30
					}
				}
			],
			yAxis : [
				{
					name: "缓存命中率(%)",
					type: "value",
					max: 100,
					splitLine: {
						show: false
					}
				}
			],
			series : [
				{
					name: "缓存命中率(%)",
					type:'bar',
					barWidth: "30px",
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					data:scope.hits.$map(function (k, v) {
						return v.percent;
					})
				}
			]
		};

		chart.setOption(option);
	};

	this.renderErrorsCharts = function () {
		var chart = echarts.init(document.getElementById("errors-chart-box"));
		var scope = this;
		var option = {
			title: {
				text: "错误率排行",
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
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '20%',
				left: '6%',
				right: '6%',
				bottom: '10%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					data : scope.errors.$map(function (k, v) {
						return v.path;
					}),
					splitLine: {
						show: false
					},
					axisLabel: {
						interval: 0,
						rotate: 30
					}
				}
			],
			yAxis : [
				{
					name: "错误率(%)",
					type: "value",
					max: 100,
					splitLine: {
						show: false
					}
				}
			],
			series : [
				{
					name: "错误率(%)",
					type:'bar',
					barWidth: "30px",
					itemStyle: {
						normal: {
							color: "#c23531"
						}
					},
					data:scope.errors.$map(function (k, v) {
						return v.percent;
					})
				}
			]
		};

		chart.setOption(option);
	};

	this.renderCostsCharts = function () {
		var chart = echarts.init(document.getElementById("costs-chart-box"));
		var scope = this;
		var option = {
			title: {
				text: "请求时间排行",
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
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '20%',
				left: '6%',
				right: '6%',
				bottom: '10%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					data : scope.costs.$map(function (k, v) {
						return v.path;
					}),
					splitLine: {
						show: false
					},
					axisLabel: {
						interval: 0,
						rotate: 30
					}
				}
			],
			yAxis : [
				{
					name: "请求时间(ms)",
					type: "value",
					max: null,
					splitLine: {
						show: false
					}
				}
			],
			series : [
				{
					name: "请求时间(ms)",
					type:'bar',
					barWidth: "30px",
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					data:scope.costs.$map(function (k, v) {
						return v.ms;
					})
				}
			]
		};

		chart.setOption(option);
	};

	this.renderRequestsCharts();
	this.renderHitsCharts();
	this.renderErrorsCharts();
	this.renderCostsCharts();
});