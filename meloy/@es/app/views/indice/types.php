{tea:layout}
{tea:view highlight}

<h3>类型<span>该索引下的所有类型</span></h3>

<p class="ui message warning" ng-if="countTypes == 0">此索引下暂时还没有类型。</p>

<div ng-if="countTypes > 0">
	<table class="ui table">
		<thead>
			<tr>
				<th>名称</th>
				<th>文档数</th>
				<th>字段定义</th>
			</tr>
		</thead>
		<tr ng-repeat="(name, config) in types">
			<td class="title vertical-top"><a href="{{Tea.url('@.type', { 'serverId':server.id, 'index':index.name, 'type':name })}}">{{name}}</a></td>
			<td class="title vertical-top">{{config.count}}</td>
			<td>
				<pre class="code json">{{config.properties|pretty}}</pre>
			</td>
		</tr>
	</table>
</div>