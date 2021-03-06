<!DOCTYPE html>
<html>
<head>
	<title>MeloyAdmin - 数据管理工具</title>

	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

	{tea:inject}
	{tea:css css/semantic.min.css}
</head>
<body ng-app="app" ng-controller="controller">


<form class="ui large form" data-tea-action="login">
	<h2 class="ui blue header">
		<div class="content">
			登录MeloyAdmin - 数据管理工具
		</div>
	</h2>

	<div class="ui segment stacked">
		<div class="field">
			<div class="ui left icon input">
				<i class="ui user icon"></i>
				<input type="text" name="email" placeholder="邮箱" maxlength="128"/>
			</div>
		</div>
		<div class="field">
			<div class="ui left icon input">
				<i class="ui lock icon"></i>
				<input type="password" name="password" placeholder="密码" maxlength="30"/>
			</div>
		</div>

		<input type="submit" value="登录" class="ui button primary large fluid"/>
	</div>

	<div class="ui message small">免费加入QQ群讨论：199435611</div>
</form>

</body>
</html>