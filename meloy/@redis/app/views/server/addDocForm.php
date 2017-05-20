{tea:layout}

<h3>添加数据</h3>

<form class="ui form" data-tea-action=".addDoc">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title">键(KEY)</td>
			<td><input type="text" name="key" placeholder=""/></td>
		</tr>
		<tr>
			<td>数据类型</td>
			<td>
				<select name="type" class="ui dropdown">
					<option ng-repeat="type in types track by type.code" value="{{type.code}}">{{type.name}}({{type.code}})</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>值(VALUE)</td>
			<td>在下一步中可以设置</td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">下一步</button>
</form>