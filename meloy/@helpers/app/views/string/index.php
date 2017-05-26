{tea:layout helper}

<form class="ui form">
	<div class="ui field">
		<label>原始字符串({{origin.length}}个字符)</label>
		<textarea placeholder="原始字符串" class="small" ng-model="origin" ng-change="changeOrigin()"></textarea>
	</div>
	<div class="ui field">
		<select class="ui dropdown" ng-model="fn" ng-change="changeFn()" ng-init="fn=''">
			<option value="">转换方法</option>
			<option value="md5">MD5(md5)</option>
			<option value="sha1">Sha1 Hash(sha1)</option>
			<option value="crc32">CRC32(crc32)</option>
			<option value="base64_encode">BASE64(base64) Encode</option>
			<option value="base64_decode">BASE64(base64) Decode</option>
			<option value="urlencode">URL Encode(urlencode)</option>
			<option value="urldecode">URL Decode(urldecode)</option>
			<option value="htmlspecialchars">HTML Entity Encode(htmlspecialchars)</option>
			<option value="htmlspecialchars_decode">HTML Entity Decode(htmlspecialchars_decode)</option>
		</select>
	</div>
	<div class="ui field">
		<label>输出结果({{result.length}}个字符) &nbsp; <a href="" ng-click="exchange()" title="原始字符串和输出结果交换位置"><i class="icon exchange"></i></a> </label>
		<textarea placeholder="输出结果" class="small" ng-model="result"></textarea>
	</div>
</form>