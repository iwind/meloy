<!DOCTYPE html>
<html>
<head>
	<title>安装程序</title>

	{tea:inject}
	{tea:css css/semantic.min.css}
</head>
<body ng-app="app" ng-controller="controller">

<!-- 顶部导航 -->
<div class="ui menu inverted top-nav">
	<div class="item">
		{数据管理平台} &raquo; 安装程序
	</div>
</div>

<div class="main">
	<div class="ui message warning">要想使用该系统，需要先执行当前的安装程序。</div>

	<h3>检查系统环境</h3>

	<table class="ui table definition celled structured">
		<thead>
			<tr>
				<th class="two wide">检查项</th>
				<th class="six wide">描述</th>
				<th class="two wide">检查结果</th>
				<th>解决方法</th>
			</tr>
		</thead>

		<tr ng-repeat="option in options">
			<td class="">{{option.name}}</td>
			<td>{{option.description}}</td>
			<td>
				<div ng-if="option.isOk" class="success ui green basic button">通过</div>
				<div ng-if="!option.isOk" class="error ui red basic button">不通过</div>
			</td>
			<td>{{option.message}}</td>
		</tr>
	</table>

	<div ng-if="hasErrors" class="ui message error">
		请先修复以上表中不通过的项目，才能进入到下一步。
	</div>

	<button class="ui button primary" ng-if="!hasErrors" ng-click="next()">下一步 (2/3) &raquo;</button>
	<button class="ui button" ng-if="hasErrors" ng-click="refresh()">重新检查</button>
</div>

</body>
</html>