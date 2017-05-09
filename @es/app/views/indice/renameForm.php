{tea:layout}

<h3>改名</h3>

<form class="ui form" data-tea-action=".rename" data-tea-before="before()" data-tea-fail="fail()">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>

	<table class="ui table">
		<tr>
			<td class="title">当前名称</td>
			<td><strong>{{index.name}}</strong></td>
		</tr>
		<tr>
			<td>新名称</td>
			<td><input type="text" name="newName" placeholder="输入新名称，只能是小写的字母、数字和下划线" /></td>
		</tr>
	</table>

	<button class="ui primary button">保存</button>

	<!-- 操作中提示 -->
	<div class="ui active inverted dimmer" ng-if="loading">
		<div class="ui large text loader">正在执行，请耐心等待</div>
	</div>
</form>