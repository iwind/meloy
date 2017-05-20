{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}

<h3>数据查询
	<span>({{total}}个文档)</span>
	<span ng-if="dsl.length > 0">(在使用查询构造器查询)</span>

	<!-- 列表风格切换 -->
	<span ng-if="total > 0"><a href="" ng-click="setListStyle('json')" ng-if="listStyle == 'table'" title="切换到JSON形式"><i class="icon code"></i></a></span>
	<span ng-if="total > 0"><a href="" ng-click="setListStyle('table')" ng-if="listStyle == 'json'" title="切换到表格形式"><i class="icon table"></i></a></span>
</h3>

<!-- 查询表单 -->
<form class="ui form" data-tea-action="" data-tea-before="searchKeyword()">
	<div class="ui two fields">
		<div class="ui field">
			<div class="ui icon input">
				<input type="text" name="q" ng-model="q" ng-init="q = '{tea:$x.q}'" placeholder="输入搜索语句，类似于 name:中国"/>
				<i class="icon remove link" ng-if="q.length > 0" ng-click="clearQ()"></i>
			</div>
		</div>
		<div class="ui field">
			<button class="ui button">搜索</button>
		</div>
	</div>
</form>

<p class="ui message warning" ng-if="total == 0">
	此类型下暂时还没有相关的文档。
</p>

<div ng-if="total > 0" ng-cloak="" id="docs-box">
	<div ng-bind-html="page|allow"></div>

	<div class="table-box" ng-if="listStyle == 'table'">
		<table class="ui table">
			<thead>
				<tr>
					<th ng-repeat="field in fields">{{field}}</th>
					<th></th>
				</tr>
			</thead>
			<tr ng-repeat="doc in docs">
				<td ng-repeat="field in fields">{{doc.array[field]}}&nbsp;</td>
				<td nowrap=""><a href="" ng-click="deleteDoc(doc)">删除</a></td>
			</tr>
		</table>
	</div>

	<table class="ui table" ng-if="listStyle == 'json'">
		<tr ng-repeat="doc in docs">
			<td class="doc-box">
				<!-- 操作按钮 -->
				<div class="menu">
					<a href="" ng-click="deleteDoc(doc)">删除</a>
				</div>

				<!-- 文档JSON -->
				<pre class="doc json" ng-bind="doc.json" ng-class="{'full':doc.isOpen}" ng-click="openViewPort(doc, $index)"></pre>

				<!-- 合上按钮 -->
				<div class="ui button icon basic tiny circular collapse-button" ng-if="doc.isOpen" ng-click="closeViewPort(doc)" title="合上文档">
					<i class="compress icon"></i>
				</div>

				<!-- 操作中提示 -->
				<div class="ui active inverted dimmer" ng-if="doc.loading">
					<div class="ui large text loader">{{doc.loadingText}}</div>
				</div>
			</td>
		</tr>
	</table>

	<div ng-bind-html="page|allow"></div>
</div>