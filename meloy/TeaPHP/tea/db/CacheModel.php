<?php

namespace tea\db;

/**
 * 支持缓存的模型
 *
 * @package tea\db
 */
class CacheModel {
	private $_model;
	private $_key;
	private $_params;
	private $_life;
	private $_debug = false;

	/**
	 * 构造器
	 *
	 * @param string $modelClass 模型方法
	 * @param string $key 缓存的键值
	 * @param int $life 超时时间
	 */
	public function __construct($modelClass, $key, $life) {
		$this->_model = $modelClass;
		if (is_array($key)) {
			if (isset($key[0])) {
				$this->_key = $key[0];
			}
			if (isset($key[1])) {
				$this->_params = $key[1];
			}
		}
		else {
			$this->_key = $key;
		}
		$this->_life = $life;
	}

	/**
	 * 是否开启调试模式
	 *
	 * 开启调试模式后，会打印SQL和是否已缓存信息
	 *
	 * @param bool $debug 是否开启调试模式
	 * @return $this
	 */
	public function debug($debug = true) {
		$this->_debug = $debug;
		return $this;
	}

	/**
	 * 查找单个对象
	 *
	 * @param mixed $pk
	 * @param $result 结果集
	 * @return Model
	 */
	public function find($pk = null, $result = null) {
		$class = $this->_model;

		if (is_null($pk)) {
			return $class::query()
					->cache($this->_key, $this->_params)
					->life($this->_life)
					->result($result)
					->debug($this->_debug)
					->find();
		}
		else if (is_scalar($pk)) {
			return $class::query()
				->cache($this->_key, $this->_params)
				->life($this->_life)
				->result($result)
				->debug($this->_debug)
				->find($pk);
		}
		else if (is_array($pk)) {
			return $class::query()
				->cache($this->_key, $this->_params)
				->life($this->_life)
				->result($result)
				->attrs($pk)
				->debug($this->_debug)
				->find();
		}
		return null;
	}

	/**
	 * 根据参数查找对象
	 *
	 * @param string $pkName 主键参数名
	 * @return static
	 * @throws \Exception
	 */
	public function findX($pkName = "id") {
		$class = $this->_model;
		$model = $class::query()
			->cache($this->_key, $this->_params)
			->life($this->_life)
			->debug($this->_debug)
			->findx($pkName);
		if (!$model) {
			throw new \Exception("Record not found");
		}
		return $model;
	}

	/**
	 * 判断记录是否存在
	 *
	 * @param mixed $pk 主键值
	 * @return bool
	 */
	public function exist($pk = null) {
		$class = $this->_model;
		return !empty($this->findOne($pk, $class::$PRIMARY_KEY));
	}

	/**
	 * 查找一条数据
	 *
	 * @param scalar|null $pk 主键值
	 * @param string|array|null $result 返回的字段
	 * @return array|null
	 */
	public function findOne($pk = null, $result = null) {
		$class = $this->_model;
		if (is_null($pk)) {
			return $class::query()
				->cache($this->_key, $this->_params)
				->life($this->_life)
				->result($result)
				->debug($this->_debug)
				->findOne();
		}
		else if (is_scalar($pk)) {
			return $class::query()
				->cache($this->_key, $this->_params)
				->life($this->_life)
				->result($result)
				->debug($this->_debug)
				->findOne($pk);
		}
		else if (is_array($pk)) {
			return $class::query()
				->cache($this->_key, $this->_params)
				->life($this->_life)
				->result($result)
				->attrs($pk)
				->debug($this->_debug)
				->findOne();
		}
		return null;
	}
}

?>