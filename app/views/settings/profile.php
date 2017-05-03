{tea:layout}

<form class="ui form" data-tea-action=".saveProfile">
	<table class="ui table">
		<tr>
			<td class="title">昵称</td>
			<td><input type="text" name="nickname" value="{{user.nickname}}"/></td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>