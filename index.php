<?php

use tea\AngularActionView;
use tea\Tea;

define("TEA_ROOT", __DIR__);
define("TEA_PUBLIC", __DIR__);
define("TEA_URL_BASE", preg_replace("{/([^/]+\\.php|\\?).*$}", "", $_SERVER["REQUEST_URI"]));
define("TEA_URL_DISPATCHER", "index.php");
define("TEA_ENABLE_ACTION_PARAM", true);

require "TeaPHP/tea.php";
require "functions.php";

Tea::shared()
	->actionView(AngularActionView::class)
	->start();

?>