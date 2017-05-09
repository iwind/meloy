<?php

namespace tea;

use tea\string\Helper;

class VueActionView extends ActionView {
	public function show() {
		$data = $this->action()->data;
		$module = $this->action()->module();
		$parent = $this->action()->parent();
		$viewName = $this->action()->view();
		$base = Tea::shared()->dispatcher();
		$urlBase = rtrim(TEA_URL_BASE, "/");
		$actionParam = TEA_ENABLE_ACTION_PARAM ? "true" : "false";

		$realParent = $parent;
		if (!is_empty($module)) {
			$realParent = "/@" . $module . $parent;
		}

		//加载JS文件
		$js = "";
		$viewDir = $this->action()->moduleDir() . DS . "views" . $parent ;
		if (is_file($viewDir . DS . "{$viewName}.js")) {
			$version = Helper::idToString(filemtime($viewDir . DS . "{$viewName}.js"));
			$js = "\n<script type=\"text/javascript\" src=\"" . u("__resource__{$realParent}") . "/{$viewName}.js?v={$version}\"></script>";
		}

		//加载CSS文件
		$css = "";
		if (is_file($viewDir . DS . "{$viewName}.css")) {
			$version = Helper::idToString(filemtime($viewDir . DS . "{$viewName}.css"));
			$css = "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"" . u("__resource__{$realParent}") . "/{$viewName}.css?v={$version}\"/>";
		}

		$json = json_encode($data);
		$data->tea = (object)[
			"inject" => "<script type=\"text/javascript\">\n window.TEA = { 
	\"ACTION\": {
		\"data\":{$json},
		\"base\":\"{$base}\",
		\"module\":\"{$module}\",
		\"parent\":\"{$parent}\",
		\"actionParam\": {$actionParam}
	}	
}; \n</script>
<script type=\"text/javascript\" src=\"" . $urlBase . "/js/vue.min.js\"></script>
<script type=\"text/javascript\" src=\"" . $urlBase . "/js/vue.tea.js\"></script>{$js}{$css}"
		];

		parent::show();
	}
}

?>