{tea:layout}
{tea:js js/highlight.pack.js}
{tea:css css/highlights/idea-copy.css}

<h3>数据查询 <span>({{total}}个文档)</span></h3>

<div class="ui segment">
	<form class="ui form" data-tea-action="" data-tea-before="searchKeyword()">
		<div class="ui two fields">
			<div class="ui field">
				<input type="text" name="q" ng-model="q" ng-init="q = '{tea:$x.q}'" placeholder="输入搜索语句，类似于 name:中国"/>
			</div>
			<div class="ui field">
				<button class="ui button">搜索</button>
			</div>
		</div>
	</form>
</div>

<p class="ui message warning" ng-if="total == 0">
	此类型下暂时还没有相关的文档。
</p>

<div ng-if="total > 0" ng-cloak="" id="docs-box">
	<div ng-bind-html="page|allow"></div>

	<table class="ui table">
		<tr ng-repeat="doc in docs">
			<td class="doc-box">
				<pre class="doc json" ng-bind="doc.json" ng-class="{'full':doc.isOpen}" ng-click="openViewPort(doc, $index)"></pre>

				<div class="ui button icon basic tiny circular collapse-button" ng-if="doc.isOpen" ng-click="closeViewPort(doc)">
					<i class="compress icon"></i>
				</div>
			</td>
		</tr>
	</table>

	<div ng-bind-html="page|allow"></div>
</div>