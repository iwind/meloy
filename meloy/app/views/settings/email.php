{tea:layout}

<h3>登录邮箱</h3>

<form class="ui form" data-tea-action=".saveEmail">
	<table class="ui table">
		<tr>
			<td class="title">登录邮箱</td>
			<td><input type="text" name="email" value="{{user.email}}"/></td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>