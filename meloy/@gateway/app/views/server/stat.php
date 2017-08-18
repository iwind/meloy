{tea:layout}
{tea:js js/echarts.min.js}

<p ng-show="error.length > 0" class="ui error message">
	{{error}}
</p>

<h3 ng-if="error.length == 0">截止目前，MeloyAPI已经为你 ...</h3>

<div class="chart-box" ng-if="error.length == 0">
	<div id="days-chart-box"></div>
	<div id="requests-chart-box"></div>
	<div id="hits-chart-box"></div>
	<div id="errors-chart-box"></div>
	<div class="clear"></div>
</div>