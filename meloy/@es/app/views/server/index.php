{tea:layout}

<div class="ui message error" ng-if="error != null && error.length > 0">
	{{error}}
</div>

<table class="ui table definition celled" ng-if="error == null">
	<thead class="full-width">
		<tr>
			<th>选项</th>
			<th>选项值</th>
		</tr>
	</thead>
	<tr ng-repeat="(key, value) in info">
		<td class="middle-title">{{key}}</td>
		<td>{{value}}</td>
	</tr>
</table>