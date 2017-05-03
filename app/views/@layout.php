<!DOCTYPE html>
<html>
<head>
	{tea:inject}
	{tea:css css/semantic.min.css}
	{tea:css /__resource__/layout.css}
</head>
<body ng-app="app" ng-controller="controller">

<!-- 顶部导航 -->
<div class="ui menu inverted top-nav">
	<div class="item">
		数据管理平台
	</div>
	<div class="right menu">
		<div class="item">
			欢迎您，{{loginUserName}}
		</div>
		<a href="{tea:url settings}" class="item">设置</a>
		<a href="{tea:url logout}" class="item" title="安全退出登录">退出</a>
	</div>
</div>

<!-- 左侧主菜单 -->
<div class="menu">
	<div class="ui labeled icon menu vertical teal">
		<a href="{tea:url dashboard}" class="item" ng-class="{active:menu == 'dashboard'}">
			<i class="dashboard icon"></i>
			控制台
		</a>
		<a href="{tea:url @es}" class="item" ng-class="{active:menu == '@es'}">
			<i class="browser icon"></i>
			ES管理
		</a>

		<!--
		<a href="{tea:url users}" class="item" ng-class="{active:menu == 'users'}">
			<i class="group icon"></i>
			用户管理
		</a>-->
	</div>
</div>

<!-- 左侧子菜单 -->
<div class="sub-menu" ng-if="subMenus.length > 0">
	<div class="ui menu vertical">
		<div ng-repeat="subMenu in subMenus">
			<div class="item teal" ng-class="{active:subMenu.active}" ng-if="subMenu.url.length > 0">
				<a href="{{subMenu.url}}">{{subMenu.name}}</a>
			</div>
			<div class="item teal" ng-class="{active:subMenu.active}" ng-if="!subMenu.url || subMenu.url.length == 0">
				{{subMenu.name}}
			</div>
			<div class="item" ng-if="subMenu.items.length > 0">
				<div class="menu">
					<a href="{{item.url}}" class="teal item" ng-class="{active:item.active}" ng-repeat="item in subMenu.items">{{item.name}}</a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 右侧主操作栏 -->
<div class="main" ng-class="{'without-menu': !subMenus || subMenus.length == 0}">
	{tea:placeholder}
</div>

</body>
</html>