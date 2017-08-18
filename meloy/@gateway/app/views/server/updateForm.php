{tea:layout}

<h3>修改应用</h3>

<form class="ui form" data-tea-action=".update">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title">应用名 *</td>
			<td>
				<input type="text" name="name" placeholder="比如Gateway001" value="{{server.name}}"/>
			</td>
		</tr>
		<tr>
			<td>管理地址 *</td>
			<td>
				<div class="ui fields inline">
					<div class="ui field">
						<select name="scheme" class="ui dropdown">
							<option value="http" ng-selected="scheme == 'http'">http://</option>
							<option value="https" ng-selected="scheme == 'https'">https://</option>
						</select>
					</div>
					<div class="ui right labeled input">
						<input type="text" name="host" placeholder="比如localhost:8001" value="{{server.host}}:{{server.port}}"/>
						<div class="ui label">
							/[API]
						</div>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>Mock数据地址</td>
			<td>
				<div class="ui fields inline">
					<div class="ui fields inline">
						<div class="ui field">
							<select name="mockScheme" class="ui dropdown">
								<option value="http" ng-selected="mockScheme == 'http'">http://</option>
								<option value="https" ng-selected="mockScheme == 'https'">https://</option>
							</select>
						</div>
						<div class="ui right labeled input">
							<input type="text" name="mockHost" placeholder="比如localhost:8001" value="{{mockHost}}"/>
							<div class="ui label">
								/@mock/[API]
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>检查连接是否正常</td>
			<td>
				<input type="checkbox" name="check" value="1" checked="checked"/>
			</td>
		</tr>
	</table>

	<input type="submit" value="保存" class="ui button primary"/>
</form>