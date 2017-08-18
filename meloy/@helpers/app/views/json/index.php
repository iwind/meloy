{tea:layout helper}


<form class="ui form">
	<div class="ui field">
		<textarea ng-model="jsonData" placeholder="把JSON内容粘贴到这里来"></textarea>
	</div>
	<div>
		<button class="ui button" ng-click="format()">格式化</button>
	</div>
</form>