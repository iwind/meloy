<?php

use tea\AngularActionView;
use tea\Tea;

define("TEA_URL_DISPATCHER", "index.php");
define("TEA_ENABLE_ACTION_PARAM", true);
define("TEA_PUBLIC", __DIR__);

require "TeaPHP/tea.php";
Tea::shared()
	->actionView(AngularActionView::class)
	->start();

?>