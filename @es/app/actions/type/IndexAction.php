<?php

namespace es\app\actions\type;

use es\Query;
use tea\page\SemanticPage;

class IndexAction extends BaseAction {
	public function run() {
		$query = new Query();

		//查询总数
		$this->data->total = $this->_api->countQuery($this->_index, $this->_type, $query);

		//分页
		$page = new SemanticPage();
		$page->total(min($this->data->total, 10000)) //只显示前10000个
			->autoQuery(true);
		$this->data->page = $page->asHtml();

		//查询当前页内容
		$query->offset($page->offset());
		$query->size($page->size());
		$result = $this->_api->searchQuery($this->_index, $this->_type, $query->asArray());
		$docs = $result->hits->hits;
		$this->data->docs = array_map(function ($doc) {
			return json_unicode_to_utf8(json_encode($doc, JSON_PRETTY_PRINT));
		}, $docs);
	}
}

?>