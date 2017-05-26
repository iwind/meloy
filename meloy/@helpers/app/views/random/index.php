{tea:layout helper}
{tea:js js/jquery.min.js}
{tea:js js/semantic.min.js}

<form class="ui form">
	<div class="ui field">
		<label>输出结果({{result.length}}个字符)</label>
		<textarea placeholder="输出结果" class="small" ng-model="result"></textarea>
	</div>
	<div class="ui field">
		<label>长度</label>
		<input type="text" ng-model="length" ng-change="rand()"/>
	</div>
	<div class="ui field">
		<label>限制</label>
		<div class="ui checkbox">
			<input type="checkbox" value="1" ng-model="containsNumbers" ng-change="rand()"/> <label>包含数字</label>
		</div>
		<div class="ui checkbox">
			<input type="checkbox" value="1" ng-model="containsUppercase" ng-change="rand()"/> <label>包含大写字母</label>
		</div>
		<div class="ui checkbox">
			<input type="checkbox" value="1" ng-model="containsLowercase" ng-change="rand()"/> <label>包含小写字母</label>
		</div>
		<div class="ui checkbox">
			<input type="checkbox" value="1" ng-model="containsPunctuation" ng-change="rand()"/> <label>包含标点符号</label>
		</div>
	</div>
	<div class="ui field">
		<button type="button" ng-click="rand()" class="ui button icon left">
			<i class="icon refresh"></i>刷新
		</button>
	</div>
</form>