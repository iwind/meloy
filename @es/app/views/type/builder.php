{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}
{tea:js /__resource__/@/elastic.query.js}
{tea:js js/Array.min.js}

<h3></h3>

<div class="ui grid two column">
	<div class="column">
		<h3>{查询构造器}</h3>

		<form class="ui form" data-tea-action="" data-tea-before="search()">
			<table class="ui table">
				<thead>
					<tr>
						<th colspan="4">查询条件</th>
					</tr>
				</thead>
				<tr ng-if="query.queryFields.length == 0">
					<td colspan="4">暂无查询条件。</td>
				</tr>
				<tr ng-if="query.queryFields.length > 0" ng-repeat="item in query.queryFields">
					<td class="one wide">{{item.field}}</td>
					<td class="five wide">
						<select ng-model="item.type">
							<option ng-repeat="type in item.types" value="{{type[0]}}">{{type[1]}}</option>
						</select>
					</td>
					<td>
						&nbsp;
						<div ng-if="[ 'gt', 'lte', 'gte', 'lt', 'term', 'match' ].$contains(item.type)">
							<input type="text" ng-model="item.value" value=""/>
						</div>
					</td>
					<td class="one wide">
						<a href="" ng-click="query.deleteField($index)"><i class="icon remove link"></i></a>
					</td>
				</tr>

				<tr>
					<td colspan="4">
						<h4>选一个要查询的字段：</h4>
						<div class="fields-box">
							<a href="" ng-repeat="(fieldName, fieldConfig) in fields" ng-if="query.supportsType(fieldConfig.type)" ng-click="query.addField(fieldName, fieldConfig.type)">{{fieldName}}</a>
						</div>
					</td>
				</tr>
			</table>

			<button type="submit" class="ui button primary">执行查询</button>
		</form>
	</div>
	<div class="column">
		<h3>{JSON}</h3>
		<div class="source-code-box">
			<pre class="source-code json" ng-bind="queryJson()"></pre>
		</div>
	</div>
</div>