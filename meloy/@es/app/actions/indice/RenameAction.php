<?php

namespace es\app\actions\indice;

use es\api\DeleteIndexApi;
use es\api\GetMappingApi;
use es\api\IndicesExistApi;
use es\api\PutMappingApi;
use es\api\ReindexApi;
use tea\Must;

class RenameAction extends BaseAction {
	public function run(string $newName, Must $must) {
		$timeout = 3600;

		//校验参数
		$must->field("newName", $newName)
			->require("请输入新名称")
			->match("/^[a-z0-9_]+$/", "名称只能为小写的字母、数字、下划线的组合")
			->match("/^[^_]/", "名称不能以下划线开头")
			->if(function ($value) use ($timeout) {
				$api = $this->_server->api(IndicesExistApi::class); /** @var IndicesExistApi $api */
				$api->index($value);
				$api->timeout($timeout);
				return !$api->exist();
			}, "该名称对应的索引已存在");


		//@TODO 读取 _settings 以便在最后设置
		//@TODO 处理 _alias

		//获取所有类型的mappings
		$getMappingApi = $this->_server->api(GetMappingApi::class); /** @var GetMappingApi $getMappingApi */
		$getMappingApi->index($this->_index);
		$getMappingApi->timeout($timeout);
		$mappings = $getMappingApi->getAll();

		//创新新的索引
		$putMappingApi = $this->_server->api(PutMappingApi::class);/** @var PutMappingApi $putMappingApi */
		$putMappingApi->index($newName);
		$putMappingApi->timeout($timeout);
		$putMappingApi->putAll($mappings);

		//转移数据
		$reindexApi = $this->_server->api(ReindexApi::class);/** @var ReindexApi $reindexApi */
		$reindexApi->sourceIndex($this->_index);
		$reindexApi->destIndex($newName);
		$reindexApi->timeout($timeout);
		$reindexApi->waitForCompletion(true);
		$reindexApi->refresh();
		$reindexApi->exec();

		//删除当前索引
		$deleteIndexApi = $this->_server->api(DeleteIndexApi::class);/** @var DeleteIndexApi $deleteIndexApi */
		$deleteIndexApi->index($this->_index);
		$deleteIndexApi->timeout($timeout);
		$deleteIndexApi->delete();

		//跳转
		$this->next(".index", [
			"serverId" => $this->_server->id,
			"index" => $newName
		]);

		$this->success("修改成功");
	}
}

?>