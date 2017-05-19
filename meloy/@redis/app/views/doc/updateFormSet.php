{tea:layout}

<!-- 修改子元素 -->
<h3>"{{key}}"的子元素 <span>(共{{count}}个)</span></h3>

<form class="ui form" ng-if="count > 0" id="set-table-box">
	<table class="ui table" ng-repeat="index in [1, 2, 3]">
		<thead>
			<tr>
				<th>值(VALUE)</th>
				<th class="six wide">操作</th>
			</tr>
		</thead>
		<tr ng-repeat="itemValue in items track by itemValue" ng-if="$index >= count * (index - 1) / 3 &&  $index < count * index / 3">
			<td>
				<div ng-if="updatingItem != itemValue">
					{{itemValue}}
				</div>
				<div ng-if="updatingItem == itemValue" class="updating-box">
					<div class="ui field">
						<textarea name="value" ng-model="newItem.value"></textarea>
					</div>
					<div class="ui field">
						<button type="button" class="ui button primary" ng-click="updateItem(itemValue)">保存</button> &nbsp;
						<button type="button" class="ui button" ng-click="cancelItemUpdating(itemValue)">取消</button>
					</div>
				</div>
			</td>
			<td class="vertical-top">
				<a href="" ng-click="updateItemForm(itemValue)">编辑</a> &nbsp;
				<a href="" ng-click="deleteItem(itemValue)">删除</a>
			</td>
		</tr>
	</table>

	<div class="clear"></div>
</form>


<!-- 添加数据 -->
<h3>添加元素</h3>
<form class="ui form" data-tea-action=".addSetItem" id="add-form">
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="vertical-top">元素值</td>
			<td>
				<textarea name="value" placeholder="这里输入元素值"></textarea>
			</td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">保存</button>
</form>

<!-- 删除 -->
<h3>删除</h3>
<a href="" ng-click="deleteDoc(key)">删除此数据</a>

<!-- 改名 -->
<h3>改名</h3>

<form class="ui form" data-tea-action=".rename">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="g" value="{{g}}"/>
	<div class="ui fields">
		<div class="field">
			<input type="text" name="newKey" placeholder="新键名" value="{{key}}"/>
		</div>
		<div class="field">
			<button type="submit" class="ui button primary">保存</button>
		</div>
	</div>
</form>