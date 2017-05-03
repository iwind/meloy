<!DOCTYPE html>
<html>
<head>
	{tea:inject}
	{tea:css css/semantic.min.css}
</head>
<body ng-app="app" ng-controller="controller">


<form class="ui form" data-tea-action="login">
	<h3>登录系统</h3>
	<table class="ui table">
		<tr>
			<td style="width:4em">邮箱</td>
			<td><input type="text" name="email" maxlength="128"/></td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input type="password" name="password" maxlength="30"/></td>
		</tr>
	</table>

	<input type="submit" value="登录" class="ui button primary"/>
</form>

</body>
</html>