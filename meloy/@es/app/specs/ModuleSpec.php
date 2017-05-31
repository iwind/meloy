<?php

namespace es\app\specs;

class ModuleSpec extends \app\specs\ModuleSpec {
	protected $_name = "Meloy ES";
	protected $_menuName = "ES搜索";
	protected $_description = "提供ElasticSearch界面管理工具";
	protected $_version = "1.0";
	protected $_visible = true;
	protected $_icon;
	protected $_developer = "Meloy Team";
	protected $_serverTypes = [ "es" ];
}

?>