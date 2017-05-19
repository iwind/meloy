{tea:layout}

<!-- 修改子元素 -->
<h3>"{{key}}"的子元素</h3>

<p class="ui message warning" ng-if="countValidItems() == 0 && newElements.length == 0">还没有子元素，可以点击"添加子元素"按钮添加。</p>

<form class="ui form" data-tea-action=".updateHash">
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table definition">
		<tr ng-repeat="(itemKey, itemValue) in value">
			<td class="four wide">
				<input type="text" name="itemKeys[]" value="{{itemKey}}" placeholder="{{(itemKey.length == 0) ? '空字符串' : '' }}"/>
			</td>
			<td>
				<input type="text" name="itemValues[]" value="{{itemValue}}" placeholder="{{(itemValue.length == 0) ? '空字符串' : '' }}"/>
			</td>
			<td class="one wide"><a href="" ng-click="removeItem(itemKey)"><i class="remove icon"></i></a> </td>
		</tr>
		<tr ng-repeat="element in newElements" ng-if="newElements.length > 0">
			<td>
				<input type="text" name="itemKeys[]" placeholder="KEY"/>
			</td>
			<td>
				<input type="text" name="itemValues[]" placeholder="VALUE"/>
			</td>
			<td>
				<a href="" ng-click="removeElement($index)"><i class="remove icon"></i></a>
			</td>
		</tr>
		<tr>
			<td colspan="3"><button type="button" class="ui button" ng-click="addElement()">+ 添加子元素</button></td>
		</tr>
	</table>

	<button class="ui button primary" type="submit">保存</button>
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