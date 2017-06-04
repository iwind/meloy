{tea:layout}



<h3>团队</h3>

<p class="ui message warning" ng-if="team == null">暂时还没有创建或加入任何团队。</p>

<div ng-if="team == null" class="team-create-box">
	<a href="{{Tea.url('.createForm')}}" class="ui button primary large">创建团队</a>
	<p>创建团队后可以管理团队成员，并对团队成员进行授权。</p>
</div>

<div ng-if="team != null">
	你已加入团队"<strong>{{team.name}}</strong>"<span ng-if="isAdmin">，而且是管理者</span>。
</div>