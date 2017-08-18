{tea:layout}
{tea:js js/echarts.min.js}

<h3></h3>

<div class="ui message info" ng-if="isCollecting">正在为您收集数据，请耐心等待 ... </div>

<div class="ui message error" ng-if="error != null && error.length > 0">{{error}} <a href="" ng-click="refresh()"><i class="icon refresh"></i></a></div>

<div id="chart-container">
	<div class="chart-box" id="load-chart-box"></div>
	<div class="chart-box" id="memory-chart-box"></div>
	<div class="chart-box" id="routines-chart-box"></div>
	<div class="chart-box" id="requests-chart-box"></div>

	<div class="clear"></div>
</div>