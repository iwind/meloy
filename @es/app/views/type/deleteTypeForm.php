{tea:layout}

<h3>删除类型</h3>

<form class="ui form" data-tea-action=".deleteType" data-tea-confirm="确定要删除当前类型吗？所有数据都会丢失！" ng-show="supportsDelete">
	<input type="hidden" name="type" value="{{type.name}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="submit" value="确定删除当前类型" class="ui button primary"/>
</form>

<p class="ui error message" ng-if="!supportsDelete">ES暂不支持删除某个类型。</p>