Tea.View.scope(function () {
	this.loadRequestChart = function () {
		var scope = this;
		var element = document.getElementById("requests-chart-box");
		var chart = echarts.init(element);
		var timesWan = "";
		if (scope.stat.requests < 10000) {
			timesWan = scope.stat.requests;
		}
		else {
			timesWan = (Math.round(scope.stat.requests * 100 / 10000) / 100).toString() + "万";
		}
		console.log(timesWan);
		var option = {
			legend: {
				orient: 'vertical',
				x: 'center',
				y: 'middle',
				data:["处理请求" + timesWan + "次"]
			},
			series: [
				{
					name:'请求数',
					type:'pie',
					radius: ['100%', '90%'],
					avoidLabelOverlap: false,
					label: {
						normal: {
							show: false,
							position: 'center'
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					data:[
						{value:0, name:"处理请求" + timesWan + "次"}
					]
				}
			]
		};
		chart.setOption(option)
	};

	this.loadDaysChart = function () {
		var scope = this;
		var element = document.getElementById("days-chart-box");
		var chart = echarts.init(element);
		var option = {
			legend: {
				orient: 'vertical',
				x: 'center',
				y: 'middle',
				data:["服务" + scope.stat.days + "天"]
			},
			series: [
				{
					name:'服务天数',
					type:'pie',
					radius: ['100%', '90%'],
					avoidLabelOverlap: false,
					label: {
						normal: {
							show: false,
							position: 'center'
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					data:[
						{value:0, name:"服务" + scope.stat.days + "天"}
					]
				}
			]
		};
		chart.setOption(option)
	};

	this.loadHitsChart = function () {
		var scope = this;
		var element = document.getElementById("hits-chart-box");
		var chart = echarts.init(element);
		var timesWan = "";
		if (scope.stat.hits < 10000) {
			timesWan = scope.stat.hits;
		}
		else {
			timesWan = (Math.round(scope.stat.hits * 100 / 10000) / 100).toString() + "万";
		}
		var option = {
			legend: {
				orient: 'vertical',
				x: 'center',
				y: 'middle',
				data:["缓存提速" + timesWan+ "次"]
			},
			series: [
				{
					name:'缓存命中数',
					type:'pie',
					radius: ['100%', '90%'],
					avoidLabelOverlap: false,
					label: {
						normal: {
							show: false,
							position: 'center'
						}
					},
					itemStyle: {
						normal: {
							color: "#749f83"
						}
					},
					data:[
						{value:0, name:"缓存提速" + timesWan + "次"}
					]
				}
			]
		};
		chart.setOption(option)
	};
	this.loadErrorsChart = function () {
		var scope = this;
		var element = document.getElementById("errors-chart-box");
		var chart = echarts.init(element);
		var timesWan = "";
		if (scope.stat.errors < 10000) {
			timesWan = scope.stat.errors;
		}
		else {
			timesWan = (Math.round(scope.stat.errors * 100 / 10000) / 100).toString() + "万";
		}
		var option = {
			legend: {
				orient: 'vertical',
				x: 'center',
				y: 'middle',
				data:[ "发现错误" + timesWan + "次" ]
			},
			series: [
				{
					name:'错误数',
					type:'pie',
					radius: ['100%', '90%'],
					avoidLabelOverlap: false,
					label: {
						normal: {
							show: false,
							position: 'center'
						}
					},
					itemStyle: {
						normal: {
							color: "#c23531"
						}
					},
					data:[
						{value:0, name: "发现错误" + timesWan + "次"}
					]
				}
			]
		};
		chart.setOption(option)
	};
	this.loadDaysChart();
	this.loadRequestChart();
	this.loadHitsChart();
	this.loadErrorsChart();
});