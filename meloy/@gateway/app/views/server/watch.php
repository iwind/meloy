{tea:layout}
{tea:js js/tea.date.js}

<h3></h3>

<div id="right-box">
	<div class="ui menu vertical api-list">
		<div class="item grey" ng-if="logs.length == 0">[等待发起请求]</div>
		<a href="" ng-click="selectLog(log)" class="ui item blue" ng-repeat="log in logs" ng-class="{active:log.id == selectedLog.id}">
			{{log.request.method}} {{log.request.uri}} &nbsp; <span>AT {{log.createdAt.dateFormat("H:i:s")}}</span>
		</a>
		<div class="ui item search">
			<div class="ui icon input right labeled">
				<input type="text" placeholder="搜索URL..." ng-model="apiKeyword" ng-change="searchApi(apiKeyword)"/>
				<div class="ui label" ng-click="clearSearchKeyword()">
					<i class="ui icon remove link" ng-if="apiKeyword.length > 0"></i>
					<i class="ui icon search" ng-if="apiKeyword == null || apiKeyword.length == 0"></i>
				</div>
			</div>
		</div>
	</div>

	<div ng-if="selectedLog" class="api-box">
		<form class="ui form">
			<table class="ui table">
				<tr class="header">
					<td colspan="2">概要信息</td>
				</tr>
				<tr>
					<td>请求URL</td>
					<td>{{selectedLog.request.uri}}</td>
				</tr>
				<tr>
					<td>请求方法</td>
					<td>{{selectedLog.request.method}}</td>
				</tr>
				<tr>
					<td>状态代码</td>
					<td>{{selectedLog.response.status}}</td>
				</tr>
				<tr>
					<td class="title">请求时间</td>
					<td>{{selectedLog.createdAt.dateFormat("Y-m-d H:i:s")}}</td>
				</tr>

				<tr class="header">
					<td colspan="2">响应</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea readonly>{{selectedLog.response.data}}</textarea>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2">请求</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea readonly>{{selectedLog.request.data}}</textarea>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<div class="clear"></div>
</div>