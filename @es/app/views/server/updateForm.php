{tea:layout}

<h3>修改主机信息</h3>

<form class="ui form" data-tea-action=".update">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title">主机名</td>
			<td>
				<input type="text" name="name" placeholder="比如ES001" value="{{server.name}}"/>
			</td>
		</tr>
		<tr>
			<td>地址</td>
			<td>
				<input type="text" name="host" placeholder="比如127.0.0.1" value="{{server.host}}"/>
			</td>
		</tr>
		<tr>
			<td>端口</td>
			<td>
				<input type="text" name="port" placeholder="比如9200" value="{{server.port}}"/>
			</td>
		</tr>
		<tr>
			<td>检查连接是否正常</td>
			<td>
				<input type="checkbox" name="check" value="1" checked="checked"/>
			</td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>