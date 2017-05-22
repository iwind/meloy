{tea:layout}

<h3>添加数据</h3>

<form class="ui form" data-tea-action=".addDoc">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<input type="hidden" name="index" value="{{index.name}}"/>
	<input type="hidden" name="type" value="{{type.name}}"/>

	<table class="ui table">
		<tr>
			<td><strong>文档ID (_id)</strong></td>
			<td>
				<input type="text" name="id" placeholder="请输入文档ID"/>
			</td>
			<td>
				<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-id-field.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
			</td>
		</tr>
		<tr ng-repeat="(fieldName, config) in fields">
			<td class="title vertical-top">{{fieldName}} <span class="small">({{config.type}})</span></td>
			<td>
				<input type="hidden" name="types[{{fieldName}}]" value="{{config.type}}"/>

				<div ng-if="['string', 'text', 'keyword'].$contains(config.type)">
					<textarea name="values[{{fieldName}}]" class="small" placeholder="请输入字符串"></textarea>
				</div>

				<div ng-if="['long', 'integer', 'short', 'byte', 'double', 'float'].$contains(config.type)">
					<input type="text" name="values[{{fieldName}}]" placeholder="请输入数字"/>
				</div>

				<div ng-if="config.type == 'date'">
					<input type="text" name="values[{{fieldName}}]" placeholder="请输入日期、时间"/>
				</div>

				<div ng-if="config.type == 'boolean'">
					<select name="values[{{fieldName}}]" class="ui dropdown">
						<option value="1">是</option>
						<option value="0">否</option>
					</select>
				</div>

				<div ng-if="['binary'].$contains(config.type)">
					<textarea name="values[{{fieldName}}]" class="small" placeholder="请输入BASE64字符串"></textarea>
				</div>

				<div ng-if="['ip'].$contains(config.type)">
					<textarea name="values[{{fieldName}}]" class="small" placeholder="请输入IP"></textarea>
				</div>

				<div ng-if="['geo_point'].$contains(config.type)">
					<input type="hidden" name="values[{{fieldName}}]" value=""/>

					<div class="ui fields inline">
						<div class="ui field">
							<select class="ui dropdown" ng-model="config['format']" ng-init="config['format'] = '0'">
								<option value="0">选择格式</option>
								<option value="1">{lat:_, lon:_}</option>
								<option value="2">lat,lon</option>
								<option value="4">[lon, lat]</option>
								<option value="3">Geo Hash</option>
							</select>
						</div>
					</div>
					<div class="ui fields inline" ng-if="config['format'] == 1">
						<div class="ui field left ">
							<label>维度(Lat)</label>
							<input type="text" name="values[{{fieldName}}][lat]" size="20" placeholder="类似于39.915122" ng-model="config.lat"/>
						</div>
						<div class="ui field left ">
							<label>经度(Lon)</label>
							<input type="text" name="values[{{fieldName}}][lon]" size="20" placeholder="类似于116.403999" ng-model="config.lon"/>
						</div>
					</div>

					<div class="ui fields inline" ng-if="config['format'] == 2">
						<input type="hidden" name="values[{{fieldName}}]"  value="{{config.lat}},{{config.lon}}" />

						<div class="ui field left ">
							<label>维度(Lat)</label>
							<input type="text" name="" size="20" placeholder="类似于39.915122" ng-model="config.lat"/>
						</div>
						<div class="ui field left ">
							<label>经度(Lon)</label>
							<input type="text" name="" size="20" placeholder="类似于116.403999" ng-model="config.lon"/>
						</div>
					</div>

					<div class="ui fields inline" ng-if="config['format'] == 4">
						<div class="ui field left ">
							<label>维度(Lat)</label>
							<input type="text" name="values[{{fieldName}}][1]" size="20" ng-model="config.lat" placeholder="类似于39.915122"/>
						</div>
						<div class="ui field left ">
							<label>经度(Lon)</label>
							<input type="text" name="values[{{fieldName}}][0]" size="20" ng-model="config.lon" placeholder="类似于116.403999"/>
						</div>
					</div>

					<div class="ui fields" ng-if="config['format'] == 3">
						<input type="text" name="values[{{fieldName}}]" ng-model="config.value" class="hidden" />

						<div class="ui field">
							<input type="text" placeholder="Geo Hash，类似于drm3btev3e86" size="30" ng-model="config.hash" ng-change="config.value = config.hash" />
						</div>
					</div>
				</div>
			</td>
			<td class="one wide vertical-top">
				<div ng-if="['string', 'text', 'keyword'].$contains(config.type)">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/text.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>

				<div ng-if="['binary'].$contains(config.type)">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/binary.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>

				<div ng-if="['long', 'integer', 'short', 'byte', 'double', 'float'].$contains(config.type)">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/number.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>

				<div ng-if="config.type == 'date'">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/date.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>
				<div ng-if="config.type == 'boolean'">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/boolean.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>
				<div ng-if="['ip'].$contains(config.type)">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/ip.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>

				<div ng-if="['geo_point'].$contains(config.type)">
					<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/geo-point.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
				</div>
			</td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">保存</button>
</form>