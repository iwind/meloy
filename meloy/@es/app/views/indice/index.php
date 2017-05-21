{tea:layout}

<table class="ui table definition celled structured">
	<thead class="full-width">
	<tr>
		<th>选项</th>
		<th>选项值</th>
	</tr>
	</thead>
	<tr>
		<td>别名</td>
		<td>
			<a href="{{Tea.url('.aliases', { 'serverId':serverId, 'index': index.name })}}" ng-if="countAliases == 0">[暂时还没有别名]</a>

			<div ng-if="countAliases > 0">
				<span ng-repeat="(aliasName, _) in info.aliases" class="ui label">{{aliasName}}</span>
				&nbsp; <a href="{{Tea.url('.aliases', { 'serverId':serverId, 'index': index.name })}}">[管理别名]</a>
			</div>
		</td>
	</tr>
	<tr>
		<td class="middle-title">类型数</td>
		<td>{{countTypes}}</td>
	</tr>
	<tr>
		<td>文档数</td>
		<td>{{stats._all.primaries.docs.count}}</td>
	</tr>
	<tr>
		<td>存储空间</td>
		<td>
			{{Tea.formatBytes(stats._all.primaries.store.size_in_bytes)}} <span class="small">({{stats._all.primaries.store.size_in_bytes }} bytes)</span>
		</td>
	</tr>
</table>