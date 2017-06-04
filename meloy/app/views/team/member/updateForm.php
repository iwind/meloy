{tea:layout}

<h3>修改成员</h3>

<form class="ui form" data-tea-action=".update">
	<input type="hidden" name="userId" value="{{user.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title">成员邮箱账号 *</td>
			<td><input type="text" name="email" value="{{user.email}}" placeholder="成员登录用的邮箱账号" maxlength="120"/></td>
		</tr>
		<tr>
			<td>成员登录密码</td>
			<td><input type="password" name="password" placeholder="成员登录用的密码，留白表示不修改" maxlength="20"/></td>
		</tr>
		<tr>
			<td>成员登录密码</td>
			<td><input type="password" name="password2" placeholder="成员登录用的密码，留白表示不修改" maxlength="20"/></td>
		</tr>
		<tr>
			<td>昵称 *</td>
			<td><input type="text" name="nickname" value="{{user.nickname}}" placeholder="成员昵称" maxlength="20"/></td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">保存</button>
</form>