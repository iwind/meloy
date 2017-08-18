{tea:layout}

<p class="ui message warning">可以安全地从当前管理平台中删除此应用，并不影响该应用上的API数据。</p>

<form class="ui form" data-tea-action=".delete" data-tea-confirm="确定要删除当前应用吗？并不影响该应用上的API数据">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="submit" value="确定删除当前应用" class="ui button primary"/>
</form>