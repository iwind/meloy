{tea:layout}
{tea:js js/Array.min.js}

<h3>添加类型字段</h3>

<form class="ui form" data-tea-action=".updateFields" id="updateForm">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>
	<input type="hidden" name="type" value="{{type.name}}"/>
	<table class="ui table">
		<tr>
			<td class="title">类型名称</td>
			<td><strong>{{type.name}}</strong></td>
			<td></td>
		</tr>
		<tr ng-repeat="field in fields">
			<td>字段({{field.type}})</td>
			<td>
				<input type="hidden" name="fieldTypes[]" value="{{field.type}}"/>

				<input type="text" name="fieldNames[]" value="{{field.name}}" placeholder="字段名" ng-if="field.canModify"/>

				<div ng-if="!field.canModify">
					<input type="hidden" name="fieldNames[]" value="{{field.name}}" placeholder="字段名"/>
					{{field.name}}

					<div class="ui icon button basic circular mini" data-tooltip="目前ES不提供对类型中已有字段的删除和修改功能，只能给类型添加新的字段" data-inverted="" ng-if="$index == 0">
						<i class="icon question"></i>
					</div>
				</div>
			</td>
			<td>
				<a href="" ng-click="removeField($index)" ng-if="field.canModify"><i class="icon close"></i></a>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="button" value="添加字段" ng-if="!showFieldsBox" class="ui button" ng-click="showFields()"/>

				<div ng-if="showFieldsBox">
					<input type="button" value="完成字段添加" class="ui button" ng-click="showFields()"/> 请选择字段类型：
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