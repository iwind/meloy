{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}
{tea:js /__resource__/@/elastic.query.js}

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
						<select ng-model="item.type" class="ui dropdown">
							<option ng-repeat="type in item.types" value="{{type[0]}}">{{type[1]}}</option>
						</select>
					</td>
					<td>
						<!-- 通用 -->
						<div ng-if="item.dataType != 'boolean' && [ 'gt', 'lte', 'gte', 'lt', 'term', 'match','wildcard', 'prefix', 'fuzzy' ].$contains(item.type)">
							<input type="text" ng-model="item.value" value=""/>
						</div>

						<!-- boolean -->
						<div ng-if="item.type == 'term' && item.dataType == 'boolean'">
							<select ng-model="item.value" class="ui dropdown">
								<option value="1">true</option>
								<option value="0">false</option>
							</select>
						</div>

						<!-- query string -->
						<div ng-if="item.type == 'spec:query_string'">
							<input type="text" ng-model="item.value" value="" placeholder="比如 name:张三"/>
						</div>

						<!-- script -->
						<div ng-if="item.type == 'spec:script'">
							<textarea ng-model="item.value" placeholder="比如 doc['id'].value&gt;0"></textarea>
						</div>
					</td>
					<td class="one wide">
						<a href="" ng-click="query.deleteField($index)"><i class="icon remove link"></i></a>
					</td>
				</tr>

				<tr>
					<td colspan="4">
						<h4>选一个要查询的字段：</h4>
						<div class="fields-box two column">
							<a href="" ng-repeat="(fieldName, fieldConfig) in fields" ng-if="query.supportsType(fieldConfig.type)" ng-click="query.addField(fieldName, fieldConfig.type)">{{fieldName}}<span>({{fieldConfig.type}})</span></a>
						</div>
					</td>
				</tr>

				<tr>
					<td colspan="4">
						<h4>其他查询</h4>
						<div class="fields-box">
							<a href="" ng-click="query.addQueryString()">查询字符串<span>(query_string)</span></a>
							<a href="" ng-click="query.addScript()">脚本<span>(script)</span></a>
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