<?php

namespace app\specs;

use app\models\server\Server;

/**
 * 服务器规约
 */
abstract class ServerSpec {
	protected $_state;
	protected $_name;
	protected $_dbTypeName = "数据库";

	/**
	 *构造器
	 *
	 * @param Server $server
	 */
	public function __construct(Server $server) {

	}

	/**
	 * 取得或设置状态
	 *
	 * @param string $state 状态代号
	 * @return $this
	 */
	public function state($state = nil) {
		if (is_nil($state)) {
			return $this->_state;
		}

		$this->_state = $state;
		return $this;
	}

	/**
	 * 取得或设置名称
	 *
	 * @param string $name
	 * @return $this
	 */
	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}

		$this->_name = $name;
		return $this;
	}

	/**
	 * 取得数据库类型名
	 */
	public function dbTypeName() {
		return $this->_dbTypeName;
	}

	/**
	 * 取得所有数据库
	 *
	 * @return DbSpec[]
	 */
	public function dbs() {
		return [];
	}

	/**
	 * 取得所有操作
	 *
	 * @return OperationSpec[]
	 */
	public function operations() {
		return [];
	}

	/**
	 * 取得某个插件的服务器规约对象
	 *
	 * @param string $module 插件
	 * @param Server $server 服务器对象
	 * @return null | self
	 */
	public static function new($module, Server $server) {
		$class = $module . "\\app\\specs\\ServerSpec";
		if (!class_exists($class)) {
			return null;
		}

		/**
		 * @var self $spec
		 */
		$spec = new $class($server);
		$spec->name($server->name);
		return $spec;
	}
}

?>