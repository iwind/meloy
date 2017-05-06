{tea:layout}

<h3>删除类型</h3>

<p class="ui message warning" ng-if="docsInIndexes > 10000">此操作对于有1万+数据的索引比较慢，请耐心等待完成。</p>

<form class="ui form" data-tea-action=".deleteType" data-tea-confirm="确定要删除当前类型吗？所有数据都会丢失！" data-tea-timeout="3600" data-tea-before="beforeDelete()" ng-show="supportsDelete">
	<input type="hidden" name="type" value="{{type.name}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="submit" value="确定删除当前类型" class="ui button primary"/>
</form>

<p class="ui error message" ng-if="!supportsDelete">ES暂不支持删除某个类型。</p>