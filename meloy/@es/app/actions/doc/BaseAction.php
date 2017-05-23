<?php

namespace es\app\actions\doc;

use app\models\server\Server;
use tea\Request;

class BaseAction extends \es\app\actions\BaseAction {
	/**
	 * 主机对象
	 *
	 * @var Server
	 */
	protected $_server;

	/**
	 * 索引名
	 *
	 * @var string
	 */
	protected $_index;

	/**
	 * 类型
	 *
	 * @var string
	 */
	protected $_type;

	public function before() {
		parent::before();

		//主机ID
		$serverId = Request::shared()->param("serverId");
		$this->data->serverId = $serverId;

		//检查主机
		$server = Server::find($serverId);
		if (!$server) {
			return 404;
		}
		$this->_server =  $server;

		$index = Request::shared()->param("index");
		$this->_index = $index;

		$type = Request::shared()->param("type");
		$this->_type = $type;
	}
}

?>