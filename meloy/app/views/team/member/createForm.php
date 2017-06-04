{tea:layout}

<h3>添加成员 <span>添加后，成员可以使用邮箱账号登录</span></h3>

<form class="ui form" data-tea-action=".create">
	<table class="ui table">
		<tr>
			<td class="title">成员邮箱账号 *</td>
			<td><input type="text" name="email" placeholder="成员登录用的邮箱账号" maxlength="120"/></td>
		</tr>
		<tr>
			<td>成员登录密码 *</td>
			<td><input type="password" name="password" placeholder="成员登录用的密码" maxlength="20"/></td>
		</tr>
		<tr>
			<td>成员登录密码 *</td>
			<td><input type="password" name="password2" placeholder="成员登录用的密码" maxlength="20"/></td>
		</tr>
		<tr>
			<td>昵称 *</td>
			<td><input type="text" name="nickname" placeholder="成员昵称" maxlength="20"/></td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">保存</button>
</form>