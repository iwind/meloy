{tea:layout}
{tea:view highlight}

<h3>添加数据</h3>

<div class="ui grid two column">
	<div class="column">
		<form class="ui form" data-tea-action=".addDoc" id="addDocForm">
			<input type="hidden" name="serverId" value="{{server.id}}"/>
			<input type="hidden" name="index" value="{{index.name}}"/>
			<input type="hidden" name="type" value="{{type.name}}"/>

			<table class="ui table">
				<tr>
					<td><strong>文档ID (_id)</strong></td>
					<td>
						<input type="text" name="id" placeholder="请输入文档ID" ng-model="docId"/>
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
							<div ng-if="config['format'] == 1">
								<div class="ui field ">
									<label>维度(Lat)</label>
									<input type="text" name="values[{{fieldName}}][lat]" size="16" placeholder="类似于39.915122" ng-model="config.lat"/>
								</div>
								<div class="ui field ">
									<label>经度(Lon)</label>
									<input type="text" name="values[{{fieldName}}][lon]" size="16" placeholder="类似于116.403999" ng-model="config.lon"/>
								</div>
							</div>

							<div ng-if="config['format'] == 2">
								<input type="hidden" name="values[{{fieldName}}]"  value="{{config.lat}},{{config.lon}}" />

								<div class="ui field ">
									<label>维度(Lat)</label>
									<input type="text" name="" size="16" placeholder="类似于39.915122" ng-model="config.lat"/>
								</div>
								<div class="ui field ">
									<label>经度(Lon)</label>
									<input type="text" name="" size="16" placeholder="类似于116.403999" ng-model="config.lon"/>
								</div>
							</div>

							<div ng-if="config['format'] == 4">
								<div class="ui field ">
									<label>维度(Lat)</label>
									<input type="text" name="values[{{fieldName}}][1]" size="16" ng-model="config.lat" placeholder="类似于39.915122"/>
								</div>
								<div class="ui field ">
									<label>经度(Lon)</label>
									<input type="text" name="values[{{fieldName}}][0]" size="16" ng-model="config.lon" placeholder="类似于116.403999"/>
								</div>
							</div>

							<div class="ui fields" ng-if="config['format'] == 3">
								<input type="text" name="values[{{fieldName}}]" ng-model="config.value" class="hidden" />

								<div class="ui field">
									<input type="text" placeholder="Geo Hash，类似于drm3btev3e86" size="30" ng-model="config.hash" ng-change="config.value = config.hash" />
								</div>
							</div>
						</div>

						<div ng-if="['geo_shape'].$contains(config.type)" ng-init="config.points = [[null, null]]">
							<div class="ui field">
								<select class="ui dropdown" ng-model="config.shape">
									<option value="">选择形状</option>
									<option value="point">点(point)</option>
									<option value="linestring">线(linestring)</option>
									<option value="polygon">多边形(polygon)</option>
									<option value="multipoint">多个点(multipoint)</option>
									<option value="multilinestring">多个线(multilinestring)</option>
									<option value="multipolygon">多个多边形(multipolygon)</option>
									<option value="geometrycollection">形状集合(geometrycollection)</option>
									<option value="envelope">矩形(envelope)</option>
									<option value="circle">圆(circle)</option>
								</select>
							</div>

							<input type="hidden" name="values[{{fieldName}}][type]" value="{{config.shape}}"/>

							<div class="ui field" ng-if="config.shape == 'point'">
								<label>X</label>
								<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][0]"/>
								<label>Y</label>
								<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][1]"/>
							</div>

							<div ng-if="[ 'linestring', 'polygon', 'multipoint' ].$contains(config.shape)">
								<div class="ui field">
									<label>X(第0个点)</label>
									<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][0][0]" ng-model="config.points[0][0]"/>
									<label>Y(第0个点)</label>
									<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][0][1]" ng-model="config.points[0][1]"/>
								</div>

								<div class="ui field" ng-repeat="v in config.points track by $index" ng-if="$index > 0">
									<label>X(第{{$index}}个点) <a href="" ng-click="removePoint(config, $index)"><i class="icon remove"></i></a></label>
									<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][{{$index}}][0]" ng-model="config.points[$index][0]"/>
									<label>Y(第{{$index}}个点)</label>
									<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][{{$index}}][1]" ng-model="config.points[$index][1]"/>
								</div>

								<div class="ui field">
									<button type="button" class="ui button" ng-click="addPoint(config)">+ 添加点</button>
								</div>
							</div>

							<div ng-if="config.shape == 'multilinestring'">
								<div ng-repeat="(lineIndex, points) in config.lines">
									<div class="ui field" ng-repeat="v in points track by $index"">
										<label>X(线{{lineIndex}}第{{$index}}个点) <a href="" ng-click="removeFromPoints(points, $index)"><i class="icon remove"></i></a></label>
										<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][{{lineIndex}}][{{$index}}][0]"/>
										<label>Y(线{{lineIndex}}第{{$index}}个点)</label>
										<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][{{lineIndex}}][{{$index}}][1]"/>
									</div>

									<div class="ui field">
										<a href="" ng-click="addToPoints(points)">+ 添加点</a> &nbsp; <a href="" ng-click="deleteLine(config.lines, lineIndex)">删除此线</a>
									</div>

									<div class="ui divider"></div>
								</div>

								<button type="button" class="ui button" ng-click="addLine(config)">+ 添加线</button>
							</div>

							<div ng-if="config.shape == 'multipolygon'">
								<div ng-repeat="(lineIndex, points) in config.lines">
									<div class="ui field" ng-repeat="v in points track by $index"">
									<label>X(多边形{{lineIndex}}第{{$index}}个点) <a href="" ng-click="removeFromPoints(points, $index)"><i class="icon remove"></i></a></label>
									<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][{{lineIndex}}][{{$index}}][0]"/>
									<label>Y(多边形{{lineIndex}}第{{$index}}个点)</label>
									<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][{{lineIndex}}][{{$index}}][1]"/>
								</div>

								<div class="ui field">
								<a href="" ng-click="addToPoints(points)">[+ 添加点]</a> &nbsp; <a href="" ng-click="deleteLine(config.lines, lineIndex)">[删除此多边形]</a>
								</div>

								<div class="ui divider"></div>
							</div>

							<button type="button" class="ui button" ng-click="addLine(config)">+ 添加多边形</button>
						</div>

							<div ng-if="config.shape == 'envelope'">
								<div class="ui field">
									<label>左上角X</label>
									<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][0][0]"/>
								</div>
								<div class="ui field">
									<label>左上角Y</label>
									<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][0][1]"/>
								</div>
								<div class="ui field">
									<label>右下角X</label>
									<input type="text" placeholder="X坐标(Lon)" name="values[{{fieldName}}][coordinates][1][0]"/>
								</div>
								<div class="ui field">
									<label>右下角Y</label>
									<input type="text" placeholder="Y坐标(Lat)" name="values[{{fieldName}}][coordinates][1][1]"/>
								</div>
							</div>

							<div ng-if="config.shape == 'circle'">
								<div class="ui field">
									<label>圆心坐标(X)</label>
									<input type="text" name="values[{{fieldName}}][coordinates][0]" placeholder="圆心坐标X(Lon)"/>
								</div>
								<div class="ui field">
									<label>圆心坐标(Y)</label>
									<input type="text" name="values[{{fieldName}}][coordinates][1]" placeholder="圆心坐标Y(Lat)"/>
								</div>
								<div class="ui fields">
									<input type="hidden" name="values[{{fieldName}}][radius]" value="{{config.radius}}{{config.radiusUnit}}"/>
									<div class="ui field">
										<label>半径</label>
										<input type="text" name="" ng-model="config.radius" placeholder="半径" size="16"/>
									</div>
									<div class="ui field">
										<label>单位</label>
										<select class="ui dropdown" ng-model="config.radiusUnit" ng-init="config.radiusUnit = 'm'">
											<option value="mm">mm(毫米)</option>
											<option value="cm">cm(厘米)</option>
											<option value="m">m(米)</option>
											<option value="km">km(千米)</option>
											<option value="in">in(英寸)</option>
											<option value="ft">ft(Feet)</option>
											<option value="yd">yd(Yards)</option>
											<option value="mi">mi(Miles)</option>
											<option value="nmi">nmi(纳米)</option>
										</select>
									</div>
								</div>
							</div>

							<div ng-if="config.shape == 'geometrycollection'">
								暂时不支持形状集合。
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

						<div ng-if="['geo_shape'].$contains(config.type)">
							<a href="https://www.elastic.co/guide/en/elasticsearch/reference/current/geo-shape.html" target="_blank" title="帮助文档"><i class="icon external"></i></a>
						</div>
					</td>
				</tr>
			</table>

			<button type="submit" class="ui button primary">保存</button>
		</form>
	</div>

	<div class="column">
		<div class="source-code-box">
			<h3>{JSON}</h3>
			<pre class="code" ng-bind="endPoint"></pre>
			<pre class="source-code code json" ng-bind="dataJson|pretty"></pre>
		</div>
	</div>
</div>