<?php

namespace es\app\actions\type;

use es\api\CountApi;

class DeleteTypeFormAction extends BaseAction {
	public function run() {
		//是否支持删除
		$this->data->supportsDelete = true;

		//当前索引数据
		/**
		 * @var CountApi $api
		 */
		$api = $this->_server->api(CountApi::class);
		$api->index($this->_index);
		$this->data->docsInIndexes = $api->count();
	}
}

?>