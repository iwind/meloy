{tea:layout}

<h3>创建索引</h3>

<form class="ui form" data-tea-action=".createIndex">
	<input type="hidden" name="serverId" value="{{server.id}}"/>

	<table class="ui table">
		<tr>
			<td class="title">名称</td>
			<td><input type="text" name="name"/></td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>
