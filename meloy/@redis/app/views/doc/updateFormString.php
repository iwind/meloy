{tea:layout}
{tea:js js/tea.date.js}
{tea:js js/jquery.min.js}
{tea:js js/jquery.tab.js}

{tea:view .menu}

<!-- 修改子元素 -->
<div ng-show="view == 'update'">
	<h3>编辑"{{key}}"
		<br/>
		<span class="label">[{{doc.type}}]</span>
		<span class="label" ng-if="doc.ttl >= 0" title="剩余时间">[TTL:{{doc.ttl}}秒/{{doc.ttlFormat}}]</span>
		<span class="label" ng-if="doc.ttl < 0" title="剩余时间">[TTL:不会超时]</span>
	</h3>

	<form class="ui form" data-tea-action=".updateString">
		<input type="hidden" name="key" value="{{key}}"/>
		<input type="hidden" name="serverId" value="{{server.id}}"/>
		<table class="ui table definition">
			<tr>
				<td class="title vertical-top">新值</td>
				<td>
					<textarea name="value" ng-allow-tab>{{value}}</textarea>
				</td>
			</tr>
		</table>
		<button type="submit" class="ui button primary">保存</button>
	</form>
</div>

<!-- 修改超时时间 -->
<div ng-show="view == 'ttl'">
	{tea:view .updateTtl}
</div>

<!-- 删除 -->
<div ng-show="view == 'delete'">
	{tea:view .updateDelete}
</div>

<!-- 改名 -->
<div ng-show="view == 'rename'">
	{tea:view .updateRename}
</div>