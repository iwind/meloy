{tea:layout}

<h3>删除索引</h3>

<form class="ui form" data-tea-action=".delete" data-tea-confirm="确定要删除当前索引吗？所有数据都会丢失！">
	<input type="hidden" name="index" value="{{index.name}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="submit" value="确定删除当前索引" class="ui button primary"/>
</form>