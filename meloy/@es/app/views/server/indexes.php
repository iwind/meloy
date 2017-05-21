{tea:layout}

<h3>索引</h3>

<div class="ui message warning" ng-if="indexes.length == 0">暂时还没有索引。</div>

<div ng-if="indexes.length > 0">
	<table class="ui table">
		<thead>
		<tr>
			<th ng-repeat="title in titles">{{title}}</th>
		</tr>
		</thead>
		<tr ng-repeat="row in indexes">
			<td ng-repeat="(index, value) in row">
				<a href="{{Tea.url('@.indice', { 'serverId':server.id, 'index':value })}}" ng-if="index == 0">{{value}}</a>
				<span ng-if="index > 0">{{value}}</span>
			</td>
		</tr>
	</table>
</div>