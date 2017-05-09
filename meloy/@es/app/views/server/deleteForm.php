{tea:layout}

<p class="ui message warning">可以安全地从当前管理平台中删除此主机，并不影响该主机上的索引数据。</p>

<form class="ui form" data-tea-action=".delete" data-tea-confirm="确定要删除当前主机吗？并不影响该主机上的索引数据">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="submit" value="确定删除当前主机" class="ui button primary"/>
</form>