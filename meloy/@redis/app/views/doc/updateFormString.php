{tea:layout}
{tea:js js/tea.date.js}

<!-- 修改子元素 -->
<h3>编辑"{{key}}"
	<span>[{{doc.type}}]</span>
	<span ng-if="doc.ttl >= 0" title="剩余时间">[TTL:{{doc.ttl}}秒/{{doc.ttlFormat}}]</span>
	<span ng-if="doc.ttl < 0" title="剩余时间">[TTL:不会超时]</span>
</h3>

<form class="ui form" data-tea-action=".updateString">
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table definition">
		<tr>
			<td class="title vertical-top">新值</td>
			<td>
				<textarea name="value">{{value}}</textarea>
			</td>
		</tr>
	</table>
	<button type="submit" class="ui button primary">保存</button>
</form>

<!-- 修改超时时间 -->
{tea:view .updateTtl}

<!-- 删除 -->
{tea:view .updateDelete}

<!-- 改名 -->
{tea:view .updateRename}