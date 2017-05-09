<!DOCTYPE html>
<html>
<head>
	<title>Meloy - 数据管理平台</title>

	{tea:inject}
	{tea:css css/semantic.min.css}
	{tea:css /__resource__/layout.css}
</head>
<body ng-app="app" ng-controller="controller">

<!-- 顶部导航 -->
<div class="ui menu inverted top-nav">
	<div class="item">
		{Meloy - 数据管理平台}
	</div>
	<div class="right menu">
		<div class="item">
			欢迎您，{{loginUserName}}
		</div>
		<a href="{tea:url settings}" class="item" ng-class="{active: menu == 'settings'}">设置</a>
		<a href="{tea:url logout}" class="item" title="安全退出登录">退出</a>
	</div>
</div>

<!-- 左侧主菜单 -->
<div class="main-menu">
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

			<!-- 第二层菜单 -->
			<div class="item" ng-if="subMenu.items.length > 0">
				<div class="menu">
					<div ng-repeat="item in subMenu.items">
						<a href="{{item.url}}" class="teal item" ng-class="{active:item.active}" >
							{{item.name}}
						</a>

						<!-- 第三层菜单 -->
						<div ng-if="item.items && item.items.length > 0" class="third-menu">
							<!--<div class="item"><strong ng-bind-html="item.items[0].name|allow"></strong></div>-->
							<div ng-repeat="(subIndex,subItem) in item.items" ng-if="subIndex > 0">
								<a class="item teal" ng-class="{active:subItem.active}" href="{{subItem.url}}"><i class="icon database"></i>{{subItem.name}}</a>

								<!-- 第四层菜单 -->
								<div ng-if="subItem.items && subItem.items.length > 0" class="fourth-menu">
									<!--<div class="item">
										<strong ng-bind-html="subItem.items[0].name|allow"></strong>
									</div>-->
									<div ng-repeat="(subSubIndex, subSubItem) in subItem.items" ng-if="subSubIndex > 0">
										<a class="item teal" ng-class="{active:subSubItem.active}" href="{{subSubItem.url}}">
											<i class="table icon"></i>{{subSubItem.name}}
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 右侧主操作栏 -->
<div class="main" ng-class="{'without-menu': !subMenus || subMenus.length == 0}">
	<!-- 操作菜单 -->
	<div class="ui top attached menu tabular tab-menu" ng-if="tabbar">
		<a class="item" ng-repeat="item in tabbar" ng-class="{active:item.active}" href="{{item.url}}">{{item.name}}</a>
	</div>

	{tea:placeholder}
</div>

<!-- 底部 -->
<div id="footer" class="ui menu inverted">
	<div class="item">v{{meloy.version}}</div>
	<a href="https://github.com/iwind/meloy" target="_blank" class="item">[GitHub]</a>
	<div class="item">免费加入QQ群讨论：199435611</div>
</div>

</body>
</html>