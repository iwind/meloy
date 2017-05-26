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

<div id="helper-box" class="large">
	<div class="ui card">
		<div class="content">
			<div class="header">计算md5 <a href="" title="关闭窗口"><i class="icon close"></i></a></div>
			<div class="description">
				{tea:placeholder}
			</div>
		</div>
		<div class="extra content">
			开发者:李白 / 模块:helpers
		</div>
	</div>
</div>


</body>
</html>