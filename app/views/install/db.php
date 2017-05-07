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
		{数据管理平台} &raquo; 安装程序 &raquo; 配置数据库
	</div>
</div>

<div class="main">
	<h3>配置数据库</h3>

	<form class="ui form" data-tea-action=".createDb">
		<table class="ui table definition celled structured">
			<tr>
				<td class="two wide">数据库主机地址</td>
				<td><input type="text" name="host" value="127.0.0.1"/></td>
			</tr>
			<tr>
				<td>数据库端口</td>
				<td><input type="text" name="port" value="3306"/></td>
			</tr>
			<tr>
				<td>数据库连接用户名</td>
				<td><input type="text" name="username" value="root"/></td>
			</tr>
			<tr>
				<td>数据库连接密码</td>
				<td><input type="text" name="password" value=""/></td>
			</tr>
			<tr>
				<td>数据库名称</td>
				<td>
					<input type="text" name="dbname" value="chaos"/>
				</td>
			</tr>
			<tr>
				<td>数据表前缀</td>
				<td>
					<input type="text" name="prefix" value="chaos_"/>
				</td>
			</tr>
		</table>

		<button type="button" class="ui button" ng-click="prev()">&laquo; 上一步</button> &nbsp;
		<button type="submit" class="ui button primary" ng-click="next()">下一步 (3/3) &raquo;</button>
	</form>

</div>

</body>
</html>