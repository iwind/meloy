<?php

namespace tea\page;

class SemanticPage extends Page {
	public function asHtml() {
		$pages = [];
		$pageNum = $this->length();
		$currPageNo = $this->current();
		$size = $this->size();
		$total = $this->total();
		$pageSetNum = $this->pageSetNum();
		$middlePageNum = ceil($pageSetNum/2);
		if ($pageNum > 0) {
			if ($currPageNo <= $middlePageNum) {
				$start = 1;
				$end = min($pageNum, $pageSetNum);
			}
			else if ($currPageNo + $middlePageNum - 1 > $pageNum) {
				$start = max(1, $pageNum - $pageSetNum - 1);
				$end = $pageNum;
			}
			else {
				$start = max(1, $currPageNo - $middlePageNum);
				$end = min($currPageNo + $middlePageNum - 1, $pageNum);
			}

			if ($currPageNo > 1) {
				$pages[] = "<a class=\"ui item\" href=\"" . $this->url(1)  . "\" title=\"首页\"><i class=\"angle double left icon\"></i></a>";
				$pages[] = "<a class=\"ui item prev\" href=\"" . $this->url($currPageNo - 1)  . "\" title=\"前 {$size}\"><i class=\"left arrow icon\"></i></a>";
			}
			else {
				$pages[] = "<a class=\"ui item disabled\" href=\"" . $this->url(1)  . "\" title=\"首页\"><i class=\"angle double left icon\"></i></a>";
				$pages[] = "<a class=\"ui item prev disabled\" href=\"" . $this->url(1)  . "\" title=\"前 {$size}\"><i class=\"left arrow icon\"></i></a>";
			}
			for ($i = $start; $i <= $end; $i++) {
				$_start = $size * ($i - 1) + 1;
				$_end = min($size * $i, $total);
				if ($i != $currPageNo) {
					$pages[] = "<a class=\"ui item\" href=\"" . $this->url($i) . "\" title=\"结果 {$_start} - {$_end}\">{$i}</a>";
				}
				else {
					$pages[] = "<a class=\"ui item active teal\" href=\"" . $this->url($i) . "\" title=\"结果 {$_start} - {$_end}\">{$i}</a>";
				}
			}
			if ($currPageNo < $pageNum) {
				$pages[] = "<a class=\"ui item next\" href=\"" . $this->url($currPageNo + 1) . "\" title=\"后 {$size}\"><i class=\"right arrow icon\"></i></a>";
				$pages[] = "<a class=\"ui item\" href=\"" . $this->url($pageNum)  . "\" title=\"尾页\"><i class=\"angle double right icon\"></i></a>";
			}
			else {
				$pages[] = "<a class=\"ui item next disabled\" href=\"" . $this->url($pageNum) . "\" title=\"后 {$size}\"><i class=\"right arrow icon\"></i></a>";
				$pages[] = "<a class=\"ui item disabled\" href=\"" . $this->url($pageNum)  . "\" title=\"尾页\"><i class=\"angle double right icon\"></i></a>";
			}
		}

		$string = implode("", $pages);

		if ($this->length() == 1) {
			return "";
		}
		return "<div class=\"ui paginate menu\">" . $string . "</div>";
	}
}

?>