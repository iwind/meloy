<!DOCTYPE html>
<html>
<head>
	<title>Meloy - 安装程序</title>

	{tea:inject}
	{tea:css css/semantic.min.css}
</head>
<body ng-app="app" ng-controller="controller">

<!-- 顶部导航 -->
<div class="ui menu inverted top-nav">
	<div class="item">
		{Meloy - 数据管理平台} &raquo; 安装程序 &raquo; 配置数据库
	</div>
</div>

<div class="main">
	<h3>配置数据库</h3>

	<form class="ui form" data-tea-action=".createDb">
		<table class="ui table definition celled structured">
			<tr>
				<td class="two wide">数据库主机地址</td>
				<td><input type="text" name="host" value="{{host}}"/></td>
			</tr>
			<tr>
				<td>数据库端口</td>
				<td><input type="text" name="port" value="{{port}}"/></td>
			</tr>
			<tr>
				<td>数据库连接用户名</td>
				<td><input type="text" name="username" value="{{db['dbs']['default']['username']}}"/></td>
			</tr>
			<tr>
				<td>数据库连接密码</td>
				<td><input type="text" name="password" value="{{db['dbs']['default']['password']}}"/></td>
			</tr>
			<tr>
				<td>数据库名称</td>
				<td>
					<div class="ui fields inline">
						<div class="ui field">
							<input type="text" name="dbname" value="{{dbname}}"/>
						</div>
						<div class="ui field">
							<label><input type="checkbox" name="autoCreateDb" value="1" checked="checked"/> 如不存在自动创建</label>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>数据表前缀</td>
				<td>
					<input type="text" name="prefix" value="{{db['default']['prefix']}}"/>
				</td>
			</tr>
		</table>

		<button type="button" class="ui button" ng-click="prev()">&laquo; 上一步</button> &nbsp;
		<button type="submit" class="ui button primary" ng-click="next()">下一步 (3/3) &raquo;</button>
	</form>

</div>

</body>
</html>