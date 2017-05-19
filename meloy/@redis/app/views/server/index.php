{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}

<div class="ui message warning" ng-if="error.length > 0">发现了问题："{{error}}"</div>

<h3>已存储的数据</h3>

<!-- 查询表单 -->
<form class="ui form" data-tea-action="" data-tea-before="searchKeyword()">
	<div class="ui two fields">
		<div class="ui field">
			<div class="ui icon input">
				<input type="text" name="q" ng-model="q" ng-init="q = '{tea:$x.q}'" placeholder="输入搜索语句，区分大小写，类似于 *user*"/>
				<i class="icon remove link" ng-if="q.length > 0" ng-click="clearQ()"></i>
			</div>
		</div>
		<div class="ui field">
			<button class="ui button">搜索</button>
		</div>
	</div>
</form>

<p class="ui message warning" ng-if="docs.length == 0">暂时还没有数据。</p>

<!-- 数据列表 -->
<table class="ui table" ng-if="docs.length > 0" id="docs-box">
	<thead>
		<tr>
			<th class="four wide">键（KEY）</th>
			<th>值（VALUE）</th>
			<th class="two wide">操作</th>
		</tr>
	</thead>
	<tr ng-repeat="doc in docs">
		<td>{{doc.key}}</td>
		<td class="value-doc">
			<span class="type-label">[{{doc.type}}]</span>
			<span ng-if="doc.count > 0" class="type-label">[共{{doc.count}}个子元素]</span>
			<pre ng-if="doc.type != 'string'" class="doc">{{doc.value}}</pre>
			<div ng-if="doc.type == 'string'">{{doc.value}}</div>
		</td>
		<td>
			<a href="{{Tea.url('@.doc.updateForm', { 'serverId': server.id, 'key': doc.key, 'g':g() })}}">编辑</a> &nbsp;
			<a href="" ng-click="deleteDoc(doc)">删除</a>
		</td>
	</tr>
</table>

<div>
	<a href="{{Tea.url('.index', { 'serverId': server.id, 'q':q })}}" ng-if="!isFirst">&laquo; 回首页</a> &nbsp;
	<a href="{{Tea.url('.index', { 'serverId': server.id, 'offset': offset, 'q':q })}}" ng-if="hasNext">加载更多 &raquo;</a>
</div>