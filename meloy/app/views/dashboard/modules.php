{tea:layout}

<h3>已安装插件</h3>

<table class="ui table" id="modules-table">
	<thead>
		<tr>
			<th class="two wide">名称</th>
			<th class="two wide">代号</th>
			<th class="one wide">版本</th>
			<th class="two wide">开发者</th>
			<th>描述</th>
			<th class="two wide">状态</th>
			<th class="one wide">操作</th>
		</tr>
	</thead>
	<tr ng-repeat="module in modules">
		<td>{{module.name}}</td>
		<td>{{module.code}}</td>
		<td>{{module.version}}</td>
		<td>{{module.developer}}</td>
		<td>{{module.description}}
			<div ng-if="module.helpers.length > 0" class="helpers-box">
				<span ng-repeat="helper in module.helpers" class="ui label" title="小助手">{{helper.name}}</span>
			</div>
		</td>
		<td>
			<a class="enabled" ng-if="module.enabled">启用中</a>
			<a class="disabled" ng-if="!module.enabled">已禁用</a>
		</td>
		<td>
			<a href="" ng-if="module.enabled" data-tea-action=".disableModule" data-code="{{module.code}}">禁用</a>
			<a href="" ng-if="!module.enabled" data-tea-action=".enableModule" data-code="{{module.code}}">启用</a>
		</td>
	</tr>
</table>