<?php

namespace gateway\app\specs;

class ModuleSpec extends \app\specs\ModuleSpec {
	protected $_name = "Meloy API";
	protected $_menuName = "API网关";
	protected $_description = "提供简单易用的网关管理程序";
	protected $_version = "1.0";
	protected $_visible = true;
	protected $_icon;
	protected $_developer = "Meloy Team";
	protected $_serverTypes = [ "gateway" ];
}

?>