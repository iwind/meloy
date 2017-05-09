<?php

namespace tea;

use tea\tpl\Parser;

class ActionView {
	private $_action;

	public function __construct(Action $action) {
		$this->_action = $action;
	}

	public function action() {
		return $this->_action;
	}

	public function set($name, $value) {
		$this->_action->data->$name = $value;
	}

	public function show() {
		$viewName = $this->_action->view();
		$name = $this->_action->name();
		$parent = $this->_action->parent();
		$moduleDir = $this->_action->moduleDir();

		$view = (strlen($viewName) == 0) ? $name : $viewName;

		$viewFile = $moduleDir . "/views" . $parent . "/" . $view . ".php";

		if (!is_file($viewFile)) {
			return;
		}

		//分析模板
		$data = (array)$this->_action->data;
		if (class_exists("tea\\tpl\\Parser")) {
			$parser = new Parser();
			$parser->parse($viewFile, $data);
		}
		else {
			extract($data);
			require $viewFile;
		}
	}
}

?>