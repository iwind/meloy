Tea.View.scope(function () {
	if (!angular.isDefined(this.selectedNodeId)) {
		this.selectedNodeId = null;
	}
	this.chartTimer = null;
	this.chartTypes = [
		{
			"name": "负载",
			"code": "load",
			"y": "(1分钟)",
			"field": "load_1m",
			"yellow": 3,
			"red": 10,
			"max": 20
		},
		{
			"name": "CPU使用率",
			"code": "cpu",
			"y": "(%)",
			"field": "cpu",
			"yellow": 50,
			"red": 80,
			"max": 100
		},
		{
			"name": "Heap使用率",
			"code": "heap",
			"y": "(%)",
			"field": "heap.percent",
			"yellow": 50,
			"red": 80,
			"max": 100
		},
		{
			"name": "RAM使用率",
			"code": "ram",
			"y": "(%)",
			"field": "ram.percent",
			"yellow": 80,
			"red": 90,
			"max": 100
		},
		{
			"name": "查询文档数",
			"code": "query-count",
			"y": "(条)",
			"field": "search.query_total",
			"yellow": -1,
			"red": -1,
			"max": -1
		}
	];
	this.charts = {};
	this.chartLabels = {};
	this.chartData = {};
	this.interval = 5000;
	this.chartDataCount = 20;

	var that = this;

	//默认选中第一个节点
	if (!this.selectedNodeId) {
		setTimeout(function () {
			if (that.nodes.length > 0) {
				that.selectNode(that.nodes[0]);
				Tea.View.update();
			}
		});
	}
	else {
		setTimeout(function () {
			var selectedNode = that.nodes.$find(function (k, v) {
				return v.id == that.selectedNodeId;
			});
			if (selectedNode) {
				that.selectNode(selectedNode);
				Tea.View.update();
			}
		});
	}

	this.selectNode = function (node) {
		this.selectedNodeId = node.id;

		this.loadChart();
	};

	this.loadChart = function () {
		var that = this;

		if (that.chartTimer != null) {
			clearTimeout(that.chartTimer);
		}

		this.chartTypes.$each(function (k, chartType) {
			var type = chartType.code;
			that.chartData[type] = [];
			that.chartLabels[type] = [];

			var chart = echarts.init(document.getElementById(type + "-chart"));
			var option = {
				title: {
					text: ''
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
					data:[ ]
				},
				toolbox: {
					feature: {
						saveAsImage: {}
					}
				},
				grid: {
					top: '18%',
					left: '1%',
					right: '4%',
					bottom: '3%',
					containLabel: true
				},
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data : []
					}
				],
				yAxis : [
					{
						name: chartType.y,
						type : 'value',
						max: (chartType.max > -1) ? chartType.max : null
					}
				],
				series : [
					{
						name: chartType.name,
						type:'line',
						smooth: true,
						stack: '总量',
						lineStyle: {
							normal: {
								color: "green"
							}
						},
						itemStyle: {
							normal: {
								color: "green"
							}
						},
						areaStyle: {normal: {
							"color": "green"
						}},
						data:[]
					}
				]
			};

			chart.setOption(option);

			that.charts[type] = chart;
		});

		//刷新数据
		this.loadData();
	};

	this.loadData = function () {
		//获取数据
		Tea.action(".monitorData")
			.post()
			.params({
				"serverId": that.server.id,
				"nodeId": that.selectedNodeId
			})
			.timeout(5)
			.success(function (response) {
				//显示数据
				this.chartTypes.$each(function (k, chartType) {
					var type = chartType.code;
					var data = response.data[chartType.field];
					var color = "green";

					if (chartType.red > -1 && data > chartType.red) {
						color = "red";
					}
					else if (chartType.yellow > -1 &&  data > chartType.yellow) {
						color = "yellow";
					}

					if (that.chartData[type].length >= that.chartDataCount) {
						that.chartData[type].shift();
						that.chartLabels[type].shift();
					}

					that.chartData[type].push(data);
					that.chartLabels[type].push(that.getTime());

					var yMax = null;
					if (chartType.max > -1) {
						var maxValue = that.chartData[type].$max();
						if (maxValue > chartType.red) {
							yMax = chartType.max;
						}
						else if (maxValue > chartType.yellow && chartType.red > -1) {
							yMax = chartType.red;
						}
						else if (chartType.yellow > -1) {
							yMax = chartType.yellow;
						}
						else if (maxValue <= chartType.max) {
							yMax = chartType.max;
						}
					}
					that.charts[type].setOption({
						xAxis: [
							{
								type: "category",
								boundaryGap: false,
								data: that.chartLabels[type]
							}
						],
						yAxis : [
							{
								name: chartType.y,
								type : 'value',
								max: yMax
							}
						],
						series: [
							{
								name: chartType.name + chartType.y,
								type: "line",
								smooth: true,
								stack: "总量",
								lineStyle: {
									normal: {
										color: color
									}
								},
								itemStyle: {
									normal: {
										color: color
									}
								},
								areaStyle: {
									normal: {
										"color": color
									}
								},
								data: that.chartData[type]
							}
						]
					});
				});

				//加载下一轮数据
				that.chartTimer = setTimeout(function () {
					that.loadData();
				}, that.interval);
			});
	};

	this.getTime = function () {
		var now = new Date();
		var hour = now.getHours();
		var minute = now.getMinutes();
		var second = now.getSeconds();

		if (hour.toString().length == 1) {
			hour = "0" + hour;
		}

		if (minute.toString().length == 1) {
			minute = "0" + minute;
		}

		if (second.toString().length == 1) {
			second = "0" + second;
		}

		return hour + ":" + minute + ":" + second;
	};
});