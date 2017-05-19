{tea:layout}

<!-- 修改子元素 -->
<h3>编辑"{{key}}"</h3>

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