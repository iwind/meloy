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
		{Meloy - 数据管理平台} &raquo; 安装程序 &raquo; 结束安装
	</div>
</div>

<div class="main">
	<h3>配置管理员登录信息</h3>

	<form class="ui form" data-tea-action=".saveAdmin">
		<table class="ui table definition celled structured">
			<tr>
				<td class="two wide">登录邮箱</td>
				<td><input type="text" name="email" value="{{user.email}}"/></td>
			</tr>
			<tr>
				<td>登录密码</td>
				<td><input type="password" name="pass" value=""/></td>
			</tr>
			<tr>
				<td>确认密码</td>
				<td><input type="password" name="pass2" value=""/></td>
			</tr>
			<tr>
				<td>昵称</td>
				<td><input type="text" name="nickname" value="{{user.nickname}}"/></td>
			</tr>
		</table>

		<button type="button" class="ui button" ng-click="prev()">&laquo; 上一步</button> &nbsp;
		<button type="submit" class="ui button primary" ng-click="next()">完成配置，去登录</button>
	</form>

</div>

</body>
</html>