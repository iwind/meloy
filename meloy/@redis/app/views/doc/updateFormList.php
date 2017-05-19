{tea:layout}

<!-- 修改子元素 -->
<h3>"{{key}}"的子元素 <span>(共{{count}}个)</span></h3>

<form class="ui form" ng-if="count > 0">
	<table class="ui table definition celled structured">
		<thead>
		<tr>
			<th>位置(INDEX)</th>
			<th>值(VALUE)</th>
			<th class="two wide">操作</th>
		</tr>
		</thead>
		<tr ng-repeat="itemValue in items track by $index">
			<td class="two wide vertical-top">{{$index + offset}}</td>
			<td>
				<div ng-if="updatingIndex != $index">{{itemValue}}</div>

				<div ng-if="updatingIndex == $index" class="updating-box">
					<div class="ui field">
						<textarea name="" ng-model="$parent.$parent.$parent.newItemValue"></textarea>
					</div>
					<div class="ui field">
						<button type="button" class="ui button primary" ng-click="updateItem($index + offset)">保存</button> &nbsp;
						<button type="button" class="ui button" ng-click="cancelItemUpdating($index)">取消</button>
					</div>
				</div>
			</td>
			<td class="vertical-top">
				<a href="" ng-click="updateItemForm($index, itemValue)">编辑</a> &nbsp;
				<a href="" ng-click="deleteItem($index + offset)">删除</a>
			</td>
		</tr>
	</table>
</form>

<div class="page" ng-bind-html="page|allow"></div>

<!-- 添加数据 -->
<h3>添加元素</h3>
<form class="ui form" data-tea-action=".addListItem">
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title vertical-top">位置</td>
			<td>
				<div class="ui field">
					<select class="ui dropdown" name="position" ng-model="newItemPosition" ng-change="changeNewItemPosition()">
						<option value="1">最后一个</option>
						<option value="2">第一个</option>
						<option value="3">自定义</option>
					</select>
				</div>
				<div class="ui fields" ng-show="newItemPosition == 3">
					<div class="ui field">
						<input type="text" name="index" ng-model="newItemIndex" value="" placeholder="支持正负值，负值表示从末尾开始读起" size="10" />
					</div>
					<div class="ui field">
						<select name="pivot" class="ui dropdown">
							<option value="after">之后</option>
							<option value="before">之前</option>
						</select>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="vertical-top">元素值</td>
			<td>
				<textarea name="value" placeholder="这里输入元素值"></textarea>
			</td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">保存</button>
</form>

<!-- 删除 -->
<h3>删除</h3>
<a href="" ng-click="deleteDoc(key)">删除此数据</a>

<!-- 改名 -->
<h3>改名</h3>

<form class="ui form" data-tea-action=".rename">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="key" value="{{key}}"/>
	<input type="hidden" name="g" value="{{g}}"/>
	<div class="ui fields">
		<div class="field">
			<input type="text" name="newKey" placeholder="新键名" value="{{key}}"/>
		</div>
		<div class="field">
			<button type="submit" class="ui button primary">保存</button>
		</div>
	</div>
</form>