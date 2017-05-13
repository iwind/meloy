{tea:layout}

<h3>节点</h3>

<a href="" id="refresh-btn" title="刷新" ng-click="reload()"><i class="icon refresh"></i></a>

<table class="ui table">
	<thead>
		<tr>
			<th>ID</th>
			<th>名称(Name)</th>
			<th>主机地址(Host)</th>
			<th>版本</th>
			<th>角色(Roles)</th>
			<th>Http端口</th>
			<th>Transport端口</th>
			<th>负载(1分钟)</th>
		</tr>
	</thead>
	<tr ng-repeat="(key,node) in nodes">
		<td>{{key}}</td>
		<td>{{node.name}}</td>
		<td>{{node.host}}</td>
		<td>{{node.version}}</td>
		<td>
			{{node.roles.join(", ")}}
		</td>
		<td>
			{{node.settings.http.port}}
		</td>
		<td>
			{{node.settings.transport.tcp.port}}
		</td>
		<td><a href="{{Tea.url('.monitor', {'serverId':server.id, 'nodeId':key})}}">{{node.load_1m}}</a></td>
	</tr>
</table>