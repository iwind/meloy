<div class="ui menu">
	<a href="" class="item" ng-class="{active:view == 'update'}" ng-click="showView('update')">编辑</a>

	<a href="" class="item" ng-class="{active:view == 'addItem'}" ng-click="showView('addItem')" ng-if="doc.type != 'string' && doc.type != 'hash'">添加元素</a>

	<a href="" class="item" ng-class="{active:view == 'ttl'}" ng-click="showView('ttl')">超时时间</a>
	<a href="" class="item" ng-class="{active:view == 'delete'}" ng-click="showView('delete')">删除</a>
	<a href="" class="item" ng-class="{active:view == 'rename'}" ng-click="showView('rename')">改名</a>
</div>