{tea:layout}

<h3>"{{user.nickname}}"的权限</h3>

<!-- 插件列表 -->
<div class="ui menu">
	<a href="{{Tea.url('.permissions', { 'userId':user.id, 'module':module.code })}}" ng-repeat="module in modules" class="ui item" ng-class="{active:selectedModule == module.code}">{{module.name}}</a>
</div>

<table class="ui table">
	<tr>
		<td colspan="5">
			<label><input type="checkbox" name="isOpen" value="1"/> 允许访问此插件</label>
		</td>
	</tr>
	<tr>
		<td class="servers-td">
			<div class="ui menu vertical blue">
				<div class="item" ng-click="selectAllServers()">
					<a href="">[选择全部]</a>
				</div>
				<div class="item" ng-repeat="server in servers" ng-class="{active: containsServer(server.id)}">
					<div class="item-left">
						<input type="checkbox" ng-checked="containsServer(server.id)" ng-click="selectServer(server)"/>
					</div>
					<div class="item-right" ng-click="showDbs(server.id)">
						{{server.name}}
						<span ng-if="server.host.length > 0"><br/>({{server.host}}:{{server.port}})</span>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</td>
		<td class="dbs-td">
			<div class="ui menu vertical" ng-if="selectedServerId > 0">
				<div class="item header">[{{dbTypeName}}]</div>
				<div class="item" ng-repeat="db in dbs">
					<div class="item-left">
						<input type="checkbox" ng-checked="containsDb(db.name)" ng-click="selectDb(db.name)"/>
					</div>
					<div class="item-right" ng-click="showTables(db.name)">
						{{db.name}}
					</div>
					<div class="clear"></div>
				</div>
			</div>

			<div class="ui menu vertical" ng-if="selectedServerId > 0">
				<div class="item header">[操作]</div>
				<div class="item" ng-repeat="operation in serverOperations">
					<div class="item-left">
						<input type="checkbox"/>
					</div>
					<div class="item-right">
						{{operation.name}}
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</td>
		<td class="tables-td">
			<div class="ui menu vertical" ng-if="selectedServerId > 0 && selectedDb.length > 0">
				<a href="" class="item header">[类型]</a>
				<a href="" class="item">table1</a>
				<a href="" class="item">table2</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
			</div>

			<div class="ui menu vertical" ng-if="selectedServerId > 0 && selectedDb.length > 0">
				<a href="" class="item header">[操作]</a>
				<a href="" class="item">table1</a>
				<a href="" class="item">table2</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
			</div>
		</td>
		<td class="permissions-td">
			<div class="ui menu vertical" ng-if="selectedServerId > 0 && selectedDb.length > 0 && selectedTable.length > 0">
				<a href="" class="item header">[操作]</a>
				<a href="" class="item">table1</a>
				<a href="" class="item">table2</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
				<a href="" class="item">table3</a>
			</div>
		</td>
		<td></td>
	</tr>
</table>