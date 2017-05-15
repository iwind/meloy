{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}

<h3>已存储的数据</h3>

<!-- 查询表单 -->
<form class="ui form" data-tea-action="" data-tea-before="searchKeyword()">
	<div class="ui two fields">
		<div class="ui field">
			<div class="ui icon input">
				<input type="text" name="q" ng-model="q" ng-init="q = '{tea:$x.q}'" placeholder="输入搜索语句，类似于 *user*"/>
				<i class="icon remove link" ng-if="q.length > 0" ng-click="clearQ()"></i>
			</div>
		</div>
		<div class="ui field">
			<button class="ui button">搜索</button>
		</div>
	</div>
</form>

<p class="ui message warning" ng-if="items.length == 0">暂时还没有数据。</p>

<table class="ui table" ng-if="items.length > 0" id="docs-box">
	<thead>
		<tr>
			<th class="four wide">键（KEY）</th>
			<th>值（VALUE）</th>
			<th class="two wide">操作</th>
		</tr>
	</thead>
	<tr ng-repeat="item in items">
		<td>{{item.key}}</td>
		<td class="value-item">
			<span class="type-label">[{{item.type}}]</span>
			<span ng-if="item.count > 0" class="type-label">[共{{item.count}}个子元素]</span>
			<pre ng-if="item.type != 'string'" class="doc">{{item.value}}</pre>
			<div ng-if="item.type == 'string'">{{item.value}}</div>
		</td>
		<td>
			<a href="" ng-click="deleteItem(item)">删除</a>
		</td>
	</tr>
</table>

<div ng-if="items.length >= 10 && offset > 0">
	<a href="{{Tea.url('.index', { 'serverId': server.id, 'q':q })}}" ng-if="!isFirst">回首页</a> &nbsp;
	<a href="{{Tea.url('.index', { 'serverId': server.id, 'offset': offset, 'q':q })}}">加载下一页</a>
</div>