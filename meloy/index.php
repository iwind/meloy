<?php

use tea\AngularActionView;
use tea\Tea;

//包含框架
require "TeaPHP/tea.php";
require "functions.php";

//启用应用
Tea::shared()
	->actionView(AngularActionView::class)
	->actionParam(true)
	->env(Tea::ENV_DEV)
	->base(preg_replace("{/([^/]+\\.php|\\?).*$}", "", $_SERVER["REQUEST_URI"] ?? ""))
	->dispatcher("index.php")
	->root(__DIR__)
	->public(__DIR__)
	->start();

?>