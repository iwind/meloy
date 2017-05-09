<?php

namespace es\app\actions\type;

use es\api\CreateIndexApi;
use es\api\DeleteIndexApi;
use es\api\GetMappingApi;
use es\api\PutMappingApi;
use es\api\ReindexApi;
use es\Exception;

class DeleteAction extends BaseAction {
	public function run() {
		//执行删除
		if (!is_empty($this->_type)) {
			try {
				//将 INDEX 改成 INDEX__TMP__
				$tmpIndex = $this->_index . "__tmp__" . time();

				//@TODO 读取 _settings 以便在最后设置
				//@TODO 处理 _alias

				/** @var GetMappingApi $getMappingApi */
				$getMappingApi = $this->_server->api(GetMappingApi::class);
				$getMappingApi->index($this->_index);
				$mappings = $getMappingApi->getAll();
				$type = $this->_type;
				unset($mappings->$type);

				$mappingNames = object_keys($mappings);
				if (!empty($mappingNames)) {
					/** @var PutMappingApi $putMappingApi */
					$putMappingApi = $this->_server->api(PutMappingApi::class);
					$putMappingApi->index($tmpIndex);
					$putMappingApi->putAll($mappings);

					/** @var ReindexApi $reindexApi */
					$reindexApi = $this->_server->api(ReindexApi::class);
					$reindexApi->sourceIndex($this->_index);
					$reindexApi->types($mappingNames);
					$reindexApi->destIndex($tmpIndex);
					$reindexApi->waitForCompletion(true);
					$reindexApi->refresh();
					$reindexApi->exec();
				}

				//删除 INDEX
				/** @var DeleteIndexApi $deleteIndexApi */
				$deleteIndexApi = $this->_server->api(DeleteIndexApi::class);
				$deleteIndexApi->index($this->_index);
				$deleteIndexApi->delete();

				//将 INDEX__TMP__ 改成 INDEX

				if (!empty($mappingNames)) {
					/** @var PutMappingApi $putMappingApi */
					$putMappingApi = $this->_server->api(PutMappingApi::class);
					$putMappingApi->index($this->_index);
					$putMappingApi->putAll($mappings);

					/** @var ReindexApi $reindexApi */
					$reindexApi = $this->_server->api(ReindexApi::class);
					$reindexApi->sourceIndex($tmpIndex);
					$reindexApi->destIndex($this->_index);
					$reindexApi->waitForCompletion(true);
					$reindexApi->refresh();
					$reindexApi->exec();

					//删除 INDEX__TMP__
					/** @var DeleteIndexApi $deleteIndexApi */
					$deleteIndexApi = $this->_server->api(DeleteIndexApi::class);
					$deleteIndexApi->index($tmpIndex);
					$deleteIndexApi->delete();
				}
				else {
					/** @var CreateIndexApi $createIndexApi */
					$createIndexApi = $this->_server->api(CreateIndexApi::class);
					$createIndexApi->index($this->_index);
					$createIndexApi->create();
				}
			} catch (Exception $e) {
				$this->fail($e->getMessage());
			}
		}

		//跳转到主机
		$this->next("@.indice", [
			"serverId" => $this->_server->id,
			"index" => $this->_index
		])->success("删除成功");
	}
}

?>