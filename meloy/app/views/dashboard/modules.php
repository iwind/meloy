{tea:layout}

<h3>已安装插件</h3>

<table class="ui table">
	<thead>
		<tr>
			<th class="two wide">名称</th>
			<th class="two wide">代号</th>
			<th class="two wide">版本</th>
			<th class="two wide">开发者</th>
			<th>描述</th>
		</tr>
	</thead>
	<tr ng-repeat="module in modules">
		<td>{{module.name}}</td>
		<td>{{module.code}}</td>
		<td>{{module.version}}</td>
		<td>{{module.developer}}</td>
		<td>{{module.description}}</td>
	</tr>
</table>