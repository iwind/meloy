{tea:layout}

{tea:js js/echarts.min.js}
{tea:js js/tea.date.js}
{tea:view highlight}

<h3></h3>

<p class="ui message error" ng-if="!success">无法从网关读取API列表，请检查网关服务是否已启动和防火墙设置是否正确。</p>

<div ng-if="success" id="right-box">
	<div class="ui menu vertical api-list">
		<div class="item grey" ng-if="apiKeyword.length > 0 && apis.length == 0">[没有找到和"{{apiKeyword}}"匹配的API]</div>
		<a href="{{Tea.url('.index', { 'serverId':server.id, 'path':api.path })}}" class="ui item blue" ng-repeat="api in apis" ng-class="{active:api.path == path}">
			{{api.path}} <span ng-if="api.name.length > 0">- {{api.name}}</span>
			<br ng-if="api.dones.length > 0 || api.todos.length > 0 || api.isDeprecated"/>
			<span class="green" ng-if="api.dones.length > 0">[done]</span>
			<span class="red" ng-if="api.todos.length > 0">[todo]</span>
			<span class="red" ng-if="api.isDeprecated">[deprecated]</span>
		</a>
		<div class="ui item search">
			<div class="ui icon input right labeled">
				<input type="text" placeholder="搜索API..." ng-model="$parent.apiKeyword" ng-change="searchApi(apiKeyword)"/>
				<div class="ui label" ng-click="clearSearchKeyword()">
					<i class="ui icon remove link" ng-if="apiKeyword.length > 0"></i>
					<i class="ui icon search" ng-if="apiKeyword == null || apiKeyword.length == 0"></i>
				</div>
			</div>
		</div>
	</div>

	<div ng-if="api" class="api-box">
		<table class="ui table" ng-cloak="">
			<tr>
				<td><strong>{{api.path}} <span ng-if="api.name.length > 0">-{{api.name}}</span></strong>

					<sup class="green" ng-if="api.dones.length > 0">[done]</sup>
					<sup class="red" ng-if="api.todos.length > 0">[todo]</sup>
					<sup class="red" ng-if="api.isDeprecated">[deprecated]</sup>

					<a href="" ng-click="refreshApi()" title="刷新"><i class="icon refresh"></i></a>
				</td>
			</tr>
			<tr class="white operations">
				<td>
					<div>
						<a href="" ng-class="{active:selectedOperation == 'chart'}" ng-click="showOperation('chart')">统计</a>
						<a href="" ng-class="{active:selectedOperation == 'request'}" ng-click="showOperation('request')">测试</a>
					</div>
				</td>
			</tr>
			<tr class="white operation chart" ng-show="selectedOperation == 'chart'">
				<td>
					<div id="stat-chart"></div>
				</td>
			</tr>
			<tr class="white operation request" ng-show="selectedOperation == 'request'">
				<td>
					<form class="ui form">
						<div class="ui fields inline">
							<div class="ui field">
								请求地址：
							</div>
							<div class="ui field">
								<select class="ui dropdown" ng-model="$parent.request.host" style="width: 10em">
									<option ng-repeat="(index,address) in api.availableAddresses" value="{{index}}">{{address.url}}</option>
								</select>
							</div>
							<div class="ui field">
								?
							</div>
							<div class="ui field">
								<input type="text" ng-model="$parent.request.query" placeholder="附加参数" size="24"/>
							</div>
						</div>
						<div class="ui fields">
							<div class="ui field">
								请求方法：
							</div>
							<div class="ui field">
								<select class="ui dropdown"  ng-model="$parent.request.method">
									<option value="{{method}}" ng-repeat="method in api.methods">{{method}}</option>
								</select>
							</div>
						</div>
						<div class="ui fields">
							<div class="ui field">
								请求内容：
							</div>
							<div class="ui field">
								<textarea name="" placeholder="比如name=libai&age=20" ng-model="$parent.request.body" class="middle"></textarea>
							</div>
						</div>
						<div class="ui fields">
							<div class="ui field">
								请求报头：
							</div>
							<div class="ui field">
								<div class="ui fields" ng-repeat="(index, _) in [0, 1, 2, 3, 4]">
									<div class="ui field">
										<input type="text" placeholder="NAME" ng-model="request.headers[index]['name']"/>
									</div>
									<div class="ui field">
										<input type="text" placeholder="VALUE"  ng-model="request.headers[index]['value']"/>
									</div>
								</div>
							</div>
						</div>
						<div class="ui fields">
							<div class="ui field">
								请求结果：
							</div>
							<div class="ui field">
								<textarea ng-model="$parent.request.response"></textarea>
							</div>
						</div>
						<div class="ui fields inline">
							<div class="ui field">
								请求耗时：
							</div>
							<div class="ui field" ng-if="$parent.request.costMs > 0">
								{{$parent.$parent.request.costMs}}ms
							</div>
							<div class="ui field" ng-if="$parent.request.costMs == 0">
								-
							</div>
						</div>
						<div class="ui fields inline">
							<div class="ui field">
								请求数量：
							</div>
							<div class="ui field">
								<select class="ui dropdown" ng-model="$parent.request.requests">
									<option value="1000">1000</option>
									<option value="2000">2000</option>
									<option value="5000">5000</option>
									<option value="10000">10000</option>
									<option value="50000">50000</option>
								</select>
							</div>
						</div>
						<div class="ui fields inline">
							<div class="ui field">
								并发数量：
							</div>
							<div class="ui field">
								<select class="ui dropdown" ng-model="$parent.request.concurrency">
									<option value="100">100</option>
									<option value="200">200</option>
									<option value="500">500</option>
									<option value="1000">1000</option>
									<option value="2000">2000</option>
								</select>
							</div>
						</div>
						<div class="ui fields" ng-if="benchmarkResult">
							<div class="ui field">
								基准测试：
							</div>
							<div class="ui field">
								<textarea class="middle">基准测试结果：
成功数量：{{benchmarkResult.success}}
失败数量：{{benchmarkResult.fail}}
每秒请求数：{{benchmarkResult.requestsPerSecond}} req/s
平均耗时：{{benchmarkResult.msPerRequest}} ms/req</textarea>
							</div>
						</div>
						<div class="ui field">
							<button class="ui button primary" ng-click="requestApi()">单次请求</button> &nbsp; <button class="ui button" ng-click="benchmarkApi()" ng-class="{loading:benchmarkTesting}">性能测试</button>
						</div>
					</form>
				</td>
			</tr>
			<tr class="header">
				<td>描述</td>
			</tr>
			<tr>
				<td>
					<p ng-if="api.description.length > 0" ng-bind-html="api.description|allow"></p>
					<p ng-if="api.description == null || api.description.length == 0">[暂无描述]</p>
				</td>
			</tr>
			<tr class="header">
				<td>请求方法</td>
			</tr>
			<tr>
				<td>
					<p ng-if="api.methods.length > 0">{{api.methods.join(" | ")}}</p>
					<p ng-if="api.methods == null || api.methods.length == 0">[暂无支持的请求方法]</p>
				</td>
			</tr>
			<tr class="header">
				<td>请求参数</td>
			</tr>
			<tr>
				<td>
					<p ng-if="api.params == null || api.params.length == 0">[暂无参数]</p>
					<p ng-if="api.params.length > 0" ng-repeat="param in api.params">
						<em>{{param.type}}</em> <strong>{{param.name}}</strong> {{param.description}}
					</p>
				</td>
			</tr>
			<tr ng-if="api.todos.length > 0" class="header">
				<td>@TODO</td>
			</tr>
			<tr ng-if="api.todos.length > 0">
				<td>
					<p ng-repeat="todo in api.todos">
						{{todo}}
					</p>
				</td>
			</tr>
			<tr ng-if="api.dones.length > 0" class="header">
				<td>已完成</td>
			</tr>
			<tr ng-if="api.dones.length > 0">
				<td>
					<p ng-repeat="done in api.dones">
						{{done}}
					</p>
				</td>
			</tr>
			<tr ng-if="api.roles.length > 0" class="header">
				<td>角色</td>
			</tr>
			<tr ng-if="api.roles.length > 0">
				<td>
					<span ng-repeat="role in api.roles">{{role}} &nbsp;</span>
				</td>
			</tr>
			<tr ng-if="(api.author != null && api.author.length > 0) || api.company.length > 0" class="header">
				<td>作者</td>
			</tr>
			<tr ng-if="(api.author != null && api.author.length > 0) || api.company.length > 0">
				<td>
					{{api.author}} &lt;{{api.company}}&gt;
				</td>
			</tr>
			<tr ng-if="api.version.length > 0" class="header">
				<td>版本</td>
			</tr>
			<tr ng-if="api.version.length > 0">
				<td>{{api.version}}</td>
			</tr>
			<tr class="header">
				<td>示例响应数据 <a href="{{server.options.mockScheme}}://{{server.options.mockHost}}/@mock{{api.path}}" ng-if="api.mock.length > 0" target="_blank" title="在新窗口中显示"><i class="icon external"></i></a></td>
			</tr>
			<tr>
				<td>
					<p ng-if="api.mock == null || api.mock.length == 0">[暂无示例响应数据]</p>
					<pre class="code" ng-if="api.mock.length > 0">{{api.mock}}</pre>
				</td>
			</tr>

			<tr class="header">
				<td>调试日志 <a href="" class="refresh-icon" title="刷新" ng-click="refreshDebugLogs(api.path)"><i class="icon refresh"></i> </a></td>
			</tr>
			<tr>
				<td>

					<p ng-if="debugLogs.length == 0">[暂无调试日志]</p>
					<div class="logs-box" ng-if="debugLogs.length > 0">
						<p ng-repeat="log in debugLogs">[{{log.createdAt.dateFormat('Y-m-d H:i:s')}}][server:{{log.server}}][host:{{log.host}}][uri:{{log.uri}}]{{log.body}}</p>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="clear"></div>
</div>