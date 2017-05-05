{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}

<p class="ui message warning" ng-if="total == 0">
	此类型下暂时还没有数据。
</p>

<h3>数据查询</h3>
<div ng-if="total > 0" id="docs-box">
	<div ng-bind-html="page|allow"></div>

	<table class="ui table">
		<tr ng-repeat="doc in docs">
			<td><pre class="doc json" ng-bind="doc"></pre></td>
		</tr>
	</table>

	<div ng-bind-html="page|allow"></div>
</div>