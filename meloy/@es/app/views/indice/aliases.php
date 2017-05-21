{tea:layout}
{tea:view highlight}

<h3>别名</h3>

<div class="ui message warning" ng-if="count == 0">暂时还没有别名。</div>

<div ng-if="count > 0">
	<table class="ui table">
		<thead>
			<tr>
				<th>别名</th>
				<th>配置</th>
				<th class="two wide">操作</th>
			</tr>
		</thead>
		<tr ng-repeat="(name, setting) in aliases">
			<td class="vertical-top title">{{name}}</td>
			<td>
				<pre class="code json">{{setting|pretty}}</pre>
			</td>
			<td class="vertical-top">
				<a href="" data-tea-action=".deleteAlias" data-index="{{index.name}}" data-serverid="{{server.id}}" data-alias="{{name}}" data-tea-confirm="确定要删除此别名吗？">删除</a>
			</td>
		</tr>
	</table>
</div>

<h3>添加别名</h3>

<form class="ui form" data-tea-action=".addAlias">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>

	<table class="ui table">
		<tr>
			<td class="title">别名</td>
			<td><input type="text" name="aliasName" placeholder="别名只能为小写的字母、数字、下划线的组合"/></td>
		</tr>
		<tr>
			<td colspan="2">
				<button class="ui button icon" type="button" ng-click="showMoreForm = !showMoreForm">
					更多选项 <i class="icon angle" ng-class="{down: !showMoreForm, up: showMoreForm}"></i>
				</button>
			</td>
		</tr>
		<tr ng-if="showMoreForm">
			<td>Routing</td>
			<td><input type="text" name="routing"/></td>
		</tr>
		<tr ng-if="showMoreForm">
			<td>Search Routing</td>
			<td><input type="text" name="search_routing"/></td>
		</tr>
		<tr ng-if="showMoreForm">
			<td>Index Routing</td>
			<td><input type="text" name="index_routing"/></td>
		</tr>
		<tr ng-if="showMoreForm">
			<td class="vertical-top">过滤(Filter)</td>
			<td>
				<textarea name="filter" placeholder="输入JSON格式的过滤条件"></textarea>
			</td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">保存</button>
</form>

<h3>帮助文档</h3>
<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-aliases.html" target="_blank">[Index Aliases]</a>