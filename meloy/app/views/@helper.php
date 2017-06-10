<!DOCTYPE html>
<html>
<head>
	<title>Meloy - 助手</title>

	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

	{tea:inject}
	{tea:css css/semantic.min.css}
	{tea:js js/Array.min.js}
	{tea:js /__resource__/helper.js}
	{tea:css /__resource__/helper.css}
</head>
<body ng-app="app" ng-controller="controller">

<div id="helper-box" class="{{_helper.size}}">
	<div class="ui card">
		<div class="content">
			<div class="header">{{_helper.name}} <a href="" title="关闭窗口" ng-click="closeHelperWindow()"><i class="icon close"></i></a></div>
			<div class="description">
				{tea:placeholder}
			</div>
		</div>
		<div class="extra content">
			 插件:{{_helper.module}} / 开发者:{{_helper.developer}} <a href="{tea:echo $_SERVER['REQUEST_URI'] ?? ''}" target="_blank" title="在新窗口中打开"><i class="icon external"></i></a>
		</div>
	</div>
</div>


</body>
</html>