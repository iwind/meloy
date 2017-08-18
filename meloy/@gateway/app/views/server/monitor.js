Tea.View.scope(function () {
	var loadData = [];
	var memoryData = [];
	var routineData = [];
	var requestData = [];

	this.isCollecting = true;

	var loadChart = echarts.init(document.getElementById("load-chart-box"));
	var memoryChart = echarts.init(document.getElementById("memory-chart-box"));
	var routineChart = echarts.init(document.getElementById("routines-chart-box"));
	var requestChart = echarts.init(document.getElementById("requests-chart-box"));

	this.renderLoadChart = function () {
		var maxY = Math.max(5, Math.ceil(loadData.$max() / 5) * 5);
		var option = {
			title: {
				text: "负载(1min)",
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
				data:[ ]
			},
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '18%',
				left: '4%',
				right: '4%',
				bottom: '3%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data : loadData,
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
					name: "负载(1min)",
					type : 'value',
					max: maxY
				}
			],
			series : [
				{
					name: "负载(1min)",
					type:'line',
					smooth: true,
					stack: '总量',
					lineStyle: {
						normal: {
							color: "#749f83"
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					areaStyle: {normal: {
						"color": "#749f83"
					}},
					showSymbol: false,
					data:loadData
				}
			]
		};

		loadChart.setOption(option);
	};

	this.renderMemoryChart = function () {
		var maxY = Math.max(200, Math.ceil(memoryData.$max() / 100) * 100);

		var option = {
			title: {
				text: "内存(M)",
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
				data:[ ]
			},
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '18%',
				left: '4%',
				right: '4%',
				bottom: '3%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data : memoryData,
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
					name: "内存(M)",
					type : 'value',
					max: maxY
				}
			],
			series : [
				{
					name: "内存(M)",
					type:'line',
					smooth: true,
					stack: '总量',
					lineStyle: {
						normal: {
							color: "#749f83"
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					areaStyle: {normal: {
						"color": "#749f83"
					}},
					showSymbol: false,
					data:memoryData
				}
			]
		};

		memoryChart.setOption(option);
	};

	this.renderRoutineChart = function () {
		var maxY = Math.max(100, Math.ceil(routineData.$max() / 100) * 100);

		var option = {
			title: {
				text: "协程数",
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
				data:[ ]
			},
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '18%',
				left: '4%',
				right: '4%',
				bottom: '3%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data : routineData,
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
					name: "协程数",
					type : 'value',
					max: maxY
				}
			],
			series : [
				{
					name: "协程数",
					type:'line',
					smooth: true,
					stack: '总量',
					lineStyle: {
						normal: {
							color: "#749f83"
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					areaStyle: {normal: {
						"color": "#749f83"
					}},
					showSymbol: false,
					data:routineData
				}
			]
		};

		routineChart.setOption(option);
	};

	this.renderRequestChart = function () {
		var maxY = Math.max(100, Math.ceil(requestData.$max() / 100) * 100);

		var option = {
			title: {
				text: "请求数",
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
				data:[ ]
			},
			toolbox: {
				feature: {
					saveAsImage: {}
				}
			},
			grid: {
				top: '18%',
				left: '4%',
				right: '4%',
				bottom: '3%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					boundaryGap : false,
					data : requestData,
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
					name: "请求数/min",
					type : 'value',
					max: maxY
				}
			],
			series : [
				{
					name: "请求数/min",
					type:'line',
					smooth: true,
					stack: '总量',
					lineStyle: {
						normal: {
							color: "#749f83"
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					areaStyle: {normal: {
						"color": "#749f83"
					}},
					showSymbol: false,
					data:requestData
				}
			]
		};

		requestChart.setOption(option);
	};

	this.renderMemoryChart();
	this.renderLoadChart();
	this.renderRoutineChart();
	this.renderRequestChart();

	this.loadData = function () {
		var scope = this;
		Tea.action(".monitorData")
			.params({
				"serverId": scope.server.id
			})
			.post()
			.success(function (response) {
				this.error = "";

				var data = response.data.data;

				//负载
				if (loadData.length > 30) {
					loadData.shift();
				}
				loadData.push(data["load1m"]);
				scope.renderLoadChart();

				this.isCollecting = (loadData.length < 5);

				//内存
				if (memoryData.length > 30) {
					memoryData.shift();
				}
				memoryData.push(Math.round(data["memory"] * 100 / 1024 / 1024) / 100);
				scope.renderMemoryChart();

				//Routine
				if (routineData.length > 30) {
					routineData.shift();
				}
				routineData.push(data["routines"]);
				scope.renderRoutineChart();

				//请求
				if (requestData.length > 30) {
					requestData.shift();
				}
				requestData.push(data["requestsPerMin"]);
				scope.renderRequestChart();

				setTimeout(function () {
					scope.loadData();
				}, 10000);
			})
			.fail(function () {
				this.error = "数据加载失败，请刷新重试";

				setTimeout(function () {
					scope.loadData();
				}, 10000);
			})
			.error(function () {
				this.error = "数据加载失败，请刷新重试";

				setTimeout(function () {
					scope.loadData();
				}, 10000);
			});
	};

	this.loadData();

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

	this.refresh = function () {
		window.location.reload();
	};
});