<?php

namespace es\app\actions\type;

use es\api\CountApi;
use es\api\SearchApi;
use es\Query;
use tea\Arrays;
use tea\page\SemanticPage;

class IndexAction extends BaseAction {
	public function run(string $q, string $dsl, string $listStyle = "json") {
		$this->data->dsl = $dsl;
		$this->data->listStyle = $listStyle;

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

		if (is_empty($dsl)) {
			$api->query($query);
		}
		else {
			$api->payload($dsl);
		}
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

		if (is_empty($dsl)) {
			$api->query($query);
		}
		else {
			$dsl = json_decode($dsl);
			$dsl->from = $page->offset();
			$dsl->size = $page->size();
			$api->payload(json_encode($dsl));
		}

		$result = $api->search();
		$docs = $result->hits->hits;
		$this->data->fields = [];
		$this->data->docs = array_map(function ($doc) use ($listStyle) {
			$obj = new \stdClass();

			if ($listStyle == "json") {
				$obj->json = json_unicode_to_utf8(json_encode($doc, JSON_PRETTY_PRINT));
			}
			else if ($listStyle == "table") {
				$obj->array = Arrays::flatten($doc);
				$this->data->fields = array_keys($obj->array);
			}
			$obj->_id = $doc->_id;
			return $obj;
		}, $docs);
	}
}

?>