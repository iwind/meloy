{tea:layout}

<h3>可管理的主机</h3>

<div class="ui grid three columns">
	<div ng-repeat="server in servers" class="ui column server">
		<div class="server-info" ng-click="selectServer(server)">
			<i class="icon computer"></i>
			<div class="address">
				<div>
					<a href="{{Tea.url(server.module + '.server', {'serverId':server.id})}}"> {{server.name}}<span>({{server.host}}:{{server.port}})</span></a>
				</div>
				<div class="type-name">
					<a href="{{Tea.url(server.module + '.server', {'serverId':server.id})}}">{{server.typeName}}</a>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="clear"></div>
</div>