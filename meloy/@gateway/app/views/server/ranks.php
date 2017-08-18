{tea:layout}
{tea:js js/echarts.min.js}

<h3 class="rank-header">
	<a href="" class="chart" ng-class="{active:chartType == 'chart'}" ng-click="selectType('chart')"><i class="icon bar chart"></i> </a>
	<a href="" class="table" ng-class="{active:chartType == 'table'}" ng-click="selectType('table')"><i class="icon table"></i> </a>
</h3>

<div class="charts-box" ng-show="chartType == null || chartType == 'chart'">
	<div id="costs-chart-box">
		<!-- 请求时间 -->
	</div>
	<div id="requests-chart-box">
		<!-- 请求次数 -->
	</div>
	<div id="hits-chart-box">
		<!-- 缓存命中率 -->
	</div>
	<div id="errors-chart-box">
		<!-- 错误率 -->
	</div>
</div>

<div class="ui two column grid" ng-show="chartType == 'table'">
	<div class="column">
		<h3>平均请求时间排行</h3>
		<table class="ui table definition celled" ng-if="costs.length > 0">
			<thead class="full-width">
			<tr>
				<th>接口</th>
				<th>请求时间</th>
			</tr>
			</thead>
			<tr ng-repeat="cost in costs">
				<td class="title">{{cost.path}}</td>
				<td>{{cost.ms}}ms</td>
			</tr>
		</table>
		<p ng-if="costs.length == 0">
			暂时还没有数据。
		</p>
	</div>
	<div class="column">
		<h3>请求数排行</h3>
		<table class="ui table definition celled" ng-if="requests.length > 0">
			<thead class="full-width">
				<tr>
					<th>接口</th>
					<th>请求次数</th>
				</tr>
			</thead>
			<tr ng-repeat="request in requests">
				<td class="title">{{request.path}}</td>
				<td>{{request.count}}</td>
			</tr>
		</table>
		<p ng-if="requests.length == 0">
			暂时还没有数据。
		</p>
	</div>

	<div class="column">
		<h3>缓存命中率排行</h3>
		<table class="ui table definition celled" ng-if="hits.length > 0">
			<thead class="full-width">
			<tr>
				<th>接口</th>
				<th>命中率</th>
			</tr>
			</thead>
			<tr ng-repeat="hit in hits">
				<td>{{hit.path}}</td>
				<td class="title">{{hit.percent}}%</td>
			</tr>
		</table>
		<p ng-if="hits.length == 0">
			暂时还没有数据。
		</p>
	</div>

	<div class="column">
		<h3>错误率排行</h3>
		<table class="ui table definition celled" ng-if="errors.length > 0">
			<thead class="full-width">
			<tr>
				<th>接口</th>
				<th>错误率</th>
			</tr>
			</thead>
			<tr ng-repeat="error in errors">
				<td class="title">{{error.path}}</td>
				<td>{{error.percent}}%</td>
			</tr>
		</table>
		<p ng-if="errors.length == 0">
			暂时还没有数据。
		</p>
	</div>
</div>