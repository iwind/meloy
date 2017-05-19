{tea:layout}

<!-- 修改子元素 -->
<h3>"{{key}}"的子元素 <span>(共{{count}}个)</span></h3>

<form class="ui form" ng-if="count > 0" id="set-table-box">
	<table class="ui table" ng-repeat="index in [1, 2]">
		<thead>
			<tr>
				<th>值(VALUE)</th>
				<th class="one wide">分值(SCORE)</th>
				<th class="four wide">操作</th>
			</tr>
		</thead>
		<tr ng-repeat="item in items track by item.value" ng-if="$index >= count * (index - 1) / 2 &&  $index < count * index / 2">
			<td>
				<div ng-if="updatingItem != item.value">{{item.value}}</div>
				<div ng-if="updatingItem == item.value" class="updating-box">
					<div class="ui fields">
						<div class="ui field four wide">分值</div>
						<div class="ui field"><input type="text" name="score" ng-model="newItem.score" value="{{item.score}}"/></div>
					</div>
					<div class="ui fields">
						<div class="ui field four wide">值</div>
						<div class="ui field">
							<textarea name="value" ng-model="newItem.value"></textarea>
						</div>
					</div>
					<div class="ui field">
						<button type="button" class="ui button primary" ng-click="updateItem(item.value)">保存</button> &nbsp;
						<button type="button" class="ui button" ng-click="cancelItemUpdating(item.value)">取消</button>
					</div>
				</div>
			</td>
			<td class="vertical-top gray ">{{item.score}}</td>
			<td class="vertical-top">
				<a href="" ng-click="updateItemForm(item)">编辑</a> &nbsp;
				<a href="" ng-click="deleteItem(item.value)">删除</a>
			</td>
		</tr>
	</table>

	<div class="clear"></div>
</form>


<!-- 添加数据 -->
<h3>添加元素</h3>
<form class="ui form" data-tea-action=".addZsetItem" id="add-form">
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td>分值(SCORE)</td>
			<td>
				<input type="text" name="score" value="1"/>
			</td>
		</tr>
		<tr>
			<td class="vertical-top">元素值(VALUE)</td>
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