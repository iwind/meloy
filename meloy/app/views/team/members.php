{tea:layout}

<h3>团队成员 <span ng-if="isAdmin"><a href="{{Tea.url('.member.createForm')}}">[添加成员]</a></span></h3>

<table class="ui table">
	<thead>
		<tr>
			<th>登录账号</th>
			<th>昵称</th>
			<th>加入时间</th>
			<th>是否为管理员</th>
			<th>状态</th>
			<th ng-if="isAdmin">操作</th>
		</tr>
	</thead>
	<tr ng-repeat="member in members">
		<td>{{member.email}}</td>
		<td>{{member.nickname}}</td>
		<td>{{member.joinedAt}}</td>
		<td>
			<a class="enabled" ng-if="member.isAdmin">Y</a>
		</td>
		<td>
			<a class="enabled" ng-if="member.state == 1">启用中</a>
			<a class="disabled" ng-if="member.state == 0">已禁用</a>
		</td>
		<td ng-if="isAdmin">
			<span ng-if="member.isAdmin">-</span>

			<span ng-if="!member.isCreator">
				<a href="{{Tea.url('team.member.updateForm', { 'userId':member.id })}}">修改</a> &nbsp;
				<a href="" ng-if="member.state == 1" data-tea-action="team.member.disable" data-user-id="{{member.id}}">禁用</a>
				<a href="" ng-if="member.state == 0" data-tea-action="team.member.enable" data-user-id="{{member.id}}">启用</a>
			</span>
		</td>
	</tr>
</table>