{tea:layout}

{tea:js js/echarts.min.js}
{tea:js js/Array.min.js}

<div class="ui menu blue" id="nodes-menu">
	<a href="" class="item" ng-repeat="node in nodes" ng-class="{active:node.id == selectedNodeId}" ng-click="selectNode(node)">{{node.name}}<span>({{node.ip}}:{{node.port}})</span></a>
</div>

<div class="ui message info" ng-if="chartData.load.length < 3">正在为您收集数据，请耐心等待 ... </div>

<div class="ui message error" ng-if="error != null && error.length > 0">{{error}} <a href="" ng-click="refresh()"><i class="icon refresh"></i></a></div>

<div id="chart-container">
	<div class="chart-box" ng-repeat="chartType in chartTypes">
		<h3>
			{{chartType.name}}
			<span class="label" ng-if="chartType.currentValue != null && chartType.maxValue != null">({{chartType.currentValue}} / {{chartType.maxValue}})</span>
		</h3>
		<div id="{{chartType.code}}-chart">
		</div>
	</div>

	<div class="clear"></div>
</div>