{tea:layout}

<form class="ui form" data-tea-action=".savePassword">
	<table class="ui table">
		<tr>
			<td class="title">昵称</td>
			<td><strong>{{user.nickname}}</strong></td>
		</tr>
		<tr>
			<td>当前密码</td>
			<td>
				<input type="password" name="pass"/>
			</td>
		</tr>
		<tr>
			<td>新密码</td>
			<td>
				<input type="password" name="newPass"/>
			</td>
		</tr>
		<tr>
			<td>重输新密码</td>
			<td>
				<input type="password" name="newPass2"/>
			</td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>