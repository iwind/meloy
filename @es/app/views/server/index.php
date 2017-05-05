{tea:layout}

<table class="ui table definition celled">
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