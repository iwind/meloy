<!-- 超时时间 -->
<h3>超时时间
	<br/>
	<span class="label" ng-if="doc.ttl >= 0" title="剩余时间">[TTL:{{doc.ttl}}秒/{{doc.ttlFormat}}]</span>
	<span class="label" ng-if="doc.ttl < 0" title="剩余时间">[TTL:不会超时]</span>
</h3>

<form class="ui form" data-tea-action=".updateTtl">
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title">修改为</td>
			<td>
				<div class="ui field">
					<select class="ui dropdown" name="ttl" ng-model="ttl" ng-init="ttl = '-1'">
						<option value="-1">不会超时</option>
						<option value="0">自定义</option>
					</select>
				</div>
				<div class="ui fields" ng-if="ttl == '0'">
					<div class="ui field">
						<input type="text" name="timeCount" size="5"/>
					</div>
					<div class="ui field">
						<select name="timeType" class="ui dropdown">
							<option value="year">年</option>
							<option value="month">月</option>
							<option value="week">周</option>
							<option value="day">天</option>
							<option value="hour">小时</option>
							<option value="minute">分钟</option>
							<option value="second">秒</option>
						</select>
					</div>
					<div class="ui field text">
						当前时间：{{now}}
					</div>
				</div>
			</td>
		</tr>
	</table>
	<button type="submit" class="ui button primary">保存</button>
</form>