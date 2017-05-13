{tea:layout}

<h3>已安装插件</h3>

<p class="ui message warning" ng-if="plugins.length == 0">暂时还未安装任何插件。</p>

<table class="ui table" ng-if="plugins.length > 0">
	<thead>
		<tr>
			<th class="three wide">节点ID</th>
			<th class="two wide">节点名</th>
			<th class="two wide">插件代号</th>
			<th class="one wide">版本</th>
			<th class="one width">描述</th>
		</tr>
	</thead>
	<tr ng-repeat="plugin in plugins">
		<td>{{plugin.id}}</td>
		<td>{{plugin.name}}</td>
		<td>{{plugin.component}}</td>
		<td>{{plugin.version}}</td>
		<td>{{plugin.description}}</td>
	</tr>
</table>

<h3>文档和帮助</h3>

<a href="https://www.elastic.co/guide/en/elasticsearch/plugins/current/index.html" target="_blank">[官网插件文档]</a>

