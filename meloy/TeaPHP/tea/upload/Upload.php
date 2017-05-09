<?php

namespace tea\upload;

use tea\Arrays;

/**
 * 上传服务
 */
class Upload {
	private $_files = [];

	/**
	 * 取得服务实例
	 *
	 * @return self 服务实例
	 */
	public static function new() {
		return new self;
	}

	/**
	 * 添加新的条目
	 *
	 * - add($field)
	 * - add([ $field, $index ])
	 * - add([ $field, $index1, $index2, ... ])
	 * - add(File $file)
	 *
	 * @param string|array|File $file 条目
	 * @param array $validator 校验规则
	 * @return null|File
	 */
	public function add($file, array $validator = []) {
		if (is_string($file)) {
			$file = new File($file);
			$file->setValidator($validator);
			$this->_files[] = $file;
		}
		else if (is_array($file) && count($file) >= 2) {
			$file = new File($file[0], array_slice($file, 1));
			$file->setValidator($validator);
			$this->_files[] = $file;
		}
		else if (is_object($file) && !($file instanceof File)) {
			/* @var File $file  */
			$file->setValidator($validator);
			$this->_files[] = $file;
		}
		else {
			$file = null;
		}
		return $file;
	}

	/**
	 * 批量添加新的条目
	 *
	 * @param array $fields 条目字段名称集合
	 * @param array $validator 校验规则
	 * @return file[]
	 */
	public function addAll(array $fields, array $validator = []) {
		$files = [];
		foreach ($fields as $field) {
			$files[] = $this->add($field, $validator);
		}
		return $files;
	}

	/**
	 * 开始接收
	 */
	public function receive() {
		foreach ($this->_files as $file) {
			/** @var File $file */
			if ($file->index() === false) {
				if (isset($_FILES[$file->field()])) {
					$this->_setFileInfo($file, $_FILES[$file->field()]);
					$file->validate();
				}
			}
			else {
				if (isset($_FILES[$file->field()]) && is_array($_FILES[$file->field()])) {
					$infos = $_FILES[$file->field()];
					$index = $file->index();

					$file->setName(Arrays::get($infos["name"], $index));
					$file->setType(Arrays::get($infos["type"], $index));
					$file->setTmp(Arrays::get($infos["tmp_name"], $index));
					$file->setError(Arrays::get($infos["error"], $index));
					$file->setSize(Arrays::get($infos["size"], $index));
					$file->validate();
				}
			}
		}
	}

	/**
	 * 接收图片
	 *
	 * @param string $field 表单字段名
	 * @param string $path 目标路径
	 * @param null|string $dir 目标路径的父级目录
	 * @return File
	 */
	public function receiveImage($field, $path, $dir = null) {
		$file = $this->add($field, [
			"ext" => [ "jpg", "jpeg", "png", "gif", "bmp" ]
		]);
		$this->receive();
		if ($file->success()) {
			$file->put($path, $dir);
		}
		return $file;
	}

	/**
	 * 设置条目信息
	 *
	 * @param File $file 条目对象
	 * @param array $info 信息
	 */
	private function _setFileInfo($file, array $info) {
		if (isset($info["name"])) {
			$file->setName($info["name"]);
		}
		if (isset($info["type"])) {
			$file->setType($info["type"]);
		}
		if (isset($info["tmp_name"])) {
			$file->setTmp($info["tmp_name"]);
		}
		if (isset($info["error"])) {
			$file->setError($info["error"]);
		}
		if (isset($info["size"])) {
			$file->setSize($info["size"]);
		}
	}

	/**
	 * 取得所有条目
	 *
	 * @return File[]
	 */
	public function files() {
		return $this->_files;
	}

	/**
	 * 获取单个条目对象
	 *
	 * @param string $field 条目字段名
	 * @param integer|boolean $index 索引，用在多个同名文件选择框批量上传
	 * @return File
	 */
	public function file($field, $index = false) {
		foreach ($this->_files as $file) {
			/* @var File $file */
			if ($file->field() === $field && $file->index() === $index) {
				return $file;
			}
		}
		return null;
	}

	/**
	 * 判断是否成功
	 *
	 * @return boolean true|false
	 */
	public function success() {
		foreach ($this->_files as $file) {
			/* @var File $file */
			if (!$file->success()) {
				return false;
			}
		}
		return true;
	}
}

?>