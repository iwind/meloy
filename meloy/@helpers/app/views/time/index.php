{tea:layout helper}

{tea:js js/tea.date.js}

<form class="ui form">
	<div class="ui segment">
		<div class="ui field">
			<label>时间戳 -&gt; 日期 &nbsp; <a href="" ng-click="setNow()">[当前时间]</a> &nbsp; <a href="" ng-click="randTime()">[随机]</a></label>
			<div class="ui right labeled input">
				<input type="number" ng-model="timestamp" ng-change="timeToDate()" placeholder="请输入时间戳"/>
				<div class="ui basic label">{{timeUnit}}</div>
			</div>
		</div>
		<div class="ui field">
			<label>转换后</label>
			<input type="text" value="{{dateFromTime}}"/>
		</div>
	</div>
	<div class="ui divider"></div>
	<div class="ui segment">
		<div class="ui field">
			<label>日期 -&gt; 时间戳</label>
			<input type="text" ng-model="date" ng-change="dateToTime()" placeholder="请输入日期或类似于+7 days的语句"/>
		</div>
		<div class="ui field">
			<label>转换后</label>
			<div class="ui input right labeled">
				<input type="text" value="{{timeFromDate}}"/>
				<div class="ui label basic">s</div>
			</div>
		</div>
		<div class="ui field">
			<label>标准时间</label>
			<input type="text" value="{{dateTimeFromDate}}"/>
		</div>
	</div>
</form>