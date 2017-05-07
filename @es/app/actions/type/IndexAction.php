<?php

namespace es\app\actions\type;

use es\api\CountApi;
use es\api\SearchApi;
use es\Query;
use tea\page\SemanticPage;

class IndexAction extends BaseAction {
	public function run(string $q) {
		$query = new Query();

		//查询总数
		/**
		 * @var CountApi $api
		 */
		$api = $this->_server->api(CountApi::class);
		$api->index($this->_index)
			->type($this->_type);

		if (!is_empty($q)) {
			$query->cond($q);
		}

		$api->query($query);
		$this->data->total = $api->count();

		//分页
		$page = new SemanticPage();
		$page->total(min($this->data->total, 10000)) //只显示前10000个
			->autoQuery(true);
		$this->data->page = $page->asHtml();

		//查询当前页内容
		$query->offset($page->offset());
		$query->size($page->size());

		/**
		 * @var SearchApi $api
		 */
		$api = $this->_server->api(SearchApi::class);
		$api->index($this->_index);
		$api->type($this->_type);
		$api->query($query);

		$result = $api->search();
		$docs = $result->hits->hits;
		$this->data->docs = array_map(function ($doc) {
			$obj = new \stdClass();
			$obj->json = json_unicode_to_utf8(json_encode($doc, JSON_PRETTY_PRINT));
			$obj->_id = $doc->_id;
			return $obj;
		}, $docs);
	}
}

?>