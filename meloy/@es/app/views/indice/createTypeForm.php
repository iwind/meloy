{tea:layout}

<h3>创建类型</h3>

<form class="ui form" data-tea-action=".createType" id="createForm">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>
	<table class="ui table">
		<tr>
			<td class="title">类型名称</td>
			<td><input type="text" name="name" placeholder="输入类型名称"/></td>
			<td></td>
		</tr>
		<tr ng-repeat="field in fields">
			<td>字段({{field.type}})</td>
			<td>
				<input type="hidden" name="fieldTypes[]" value="{{field.type}}"/>
				<input type="text" name="fieldNames[]" placeholder="字段名"/>
			</td>
			<td>
				<a href="" ng-click="removeField($index)"><i class="icon close"></i></a>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="button" value="添加字段" ng-if="!showFieldsBox" class="ui button" ng-click="showFields()"/>

				<div ng-if="showFieldsBox">
					<input type="button" value="完成字段添加" class="ui button" ng-click="showFields()"/>  请选择字段类型：
				</div>

				<div ng-show="showFieldsBox">
					<div class="ui fluid field-types  transition visible scale">
						<div class="ui relaxed equal height divided grid" ng-class="{'three column':dataTypes.length == 3, 'four column':dataTypes.length == 4, 'five column':dataTypes.length == 5, 'six column':dataTypes.length == 6}">
							<div class="column" ng-repeat="group in dataTypes">
								<h4 class="ui header">{{group.name}}</h4>
								<div class="ui link list" ng-repeat="type in group.types">
									<div class="item">{{type[0]}}</div>
									<a href="" ng-repeat="subType in type[1]" ng-click="addField(subType)" class="item" style="font-size:0.8em">{{subType['code']}}</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>