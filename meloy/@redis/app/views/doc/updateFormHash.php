{tea:layout}
{tea:js js/tea.date.js}

<!-- 修改子元素 -->
<h3>"{{key}}"的子元素 <span>(共{{count}}个)</span>

	<span>[{{doc.type}}]</span>
	<span ng-if="doc.ttl >= 0" title="剩余时间">[TTL:{{doc.ttl}}秒/{{doc.ttlFormat}}]</span>
	<span ng-if="doc.ttl < 0" title="剩余时间">[TTL:不会超时]</span>
</h3>

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

<!-- 修改超时时间 -->
{tea:view .updateTtl}

<!-- 删除 -->
{tea:view .updateDelete}

<!-- 改名 -->
{tea:view .updateRename}