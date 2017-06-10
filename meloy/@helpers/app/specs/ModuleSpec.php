<?php

namespace helpers\app\specs;

use app\classes\DateHelper;
use app\specs\HelperSpec;

class ModuleSpec extends \app\specs\ModuleSpec {
	protected $_name = "小助手";
	protected $_menuName;
	protected $_description = "提供一组内置的小助手";
	protected $_version = "1.0";
	protected $_visible = false;
	protected $_icon;
	protected $_developer = "Meloy Team";

	public function __construct() {
		$stringHelper = new HelperSpec();
		$stringHelper->name("字符串转换");
		$stringHelper->code("string");
		$stringHelper->size(HelperSpec::SIZE_SMALL);
		$stringHelper->url(u("@helpers.string"));

		$randomHelper = new HelperSpec();
		$randomHelper->name("随机字符串");
		$randomHelper->code("random");
		$randomHelper->size(HelperSpec::SIZE_SMALL);
		$randomHelper->url(u("@helpers.random"));

		$dateHelper = new HelperSpec();
		$dateHelper->name("时间");
		$dateHelper->code("time");
		$dateHelper->size(HelperSpec::SIZE_SMALL);
		$dateHelper->url(u("@helpers.time"));

		/**$regularHelper = new HelperSpec();
		$regularHelper->name("正则");
		$regularHelper->code("regular");
		$regularHelper->size(HelperSpec::SIZE_SMALL);**/

		$this->_helpers = [  $stringHelper, $randomHelper, $dateHelper ];
	}
}

?>