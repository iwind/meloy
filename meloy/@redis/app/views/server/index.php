{tea:layout}
{tea:view highlight}

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
			<!-- Type -->
			<span class="type-label" title="数据类型">[{{doc.type}}]</span>

			<!-- ttl -->
			<span class="type-label" ng-if="doc.ttl >= 0" title="剩余时间">[TTL:{{doc.ttl}}秒/{{doc.ttlFormat}}]</span>
			<span class="type-label" ng-if="doc.ttl < 0" title="剩余时间">[TTL:不会超时]</span>

			<span ng-if="doc.count > 0" class="type-label">[共{{doc.count}}个子元素]</span>
			<pre ng-if="doc.type != 'string'" class="doc code json">{{doc.value}}</pre>
			<div ng-if="doc.type == 'string'">{{doc.value}}</div>

			<div ng-if="doc.realType != null" class="real-value-doc">
				<span class="type-label">[自动识别为 {{doc.realType}} ({{doc.realTypeName}})]</span>
				<pre class="doc code php" ng-bind="doc.realValue" ng-if="doc.realType == 'php serializer'"></pre>
				<pre class="doc code xml" ng-bind="doc.realValue" ng-if="doc.realType == 'xml'"></pre>
				<div class="url" ng-if="doc.realType == 'url'">
					<a href="{{doc.realValue}}" target="_blank" ng-bind="doc.realValue"></a>
				</div>
				<pre class="doc code json" ng-bind="doc.realValue" ng-if="doc.realType != 'php serializer' && doc.realType != 'xml' && doc.realType != 'url'"></pre>
			</div>
		</td>
		<td>
			<a href="{{Tea.url('@.doc.updateForm', { 'serverId': server.id, 'key': doc.key, 'g':g() })}}">编辑</a> &nbsp;
			<a href="" ng-click="deleteDoc(doc)">删除</a>
		</td>
	</tr>
</table>

<div>
	<a href="{{Tea.url('.index', { 'serverId': server.id, 'q':q })}}" ng-if="!isFirst">&laquo; 回首页</a> &nbsp;
	<a href="{{Tea.url('.index', { 'serverId': server.id, 'offset': offset, 'q':q, 'scan':scan ? 1 : 0 })}}" ng-if="hasNext">加载更多 &raquo;</a>
</div>