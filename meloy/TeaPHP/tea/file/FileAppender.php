<?php

namespace tea\file;

class FileAppender {
	/**
	 * @var File
	 */
	private $_file;
	private $_fp;

	public function __construct($file) {
		if (!($file instanceof File)) {
			$this->_file = new File($file);
		}
		else {
			$this->_file = $file;
		}
		if (!$this->_file->canWrite()) {
			$this->_fp = @fopen($this->_file->path(), "a+");

			if (!$this->_fp) {
				throw new Exception("The file is not writeable");
			}
			chmod($this->_file->absPath(), 0777);
		}
		else {
			$this->_fp = fopen($this->_file->absPath(), "a+");
		}
	}

	public function close() {
		fclose($this->_fp);
		$this->_fp = null;
	}

	private function _check() {
		if (!$this->_fp) {
			throw new Exception("File reader have already closed");
		}
	}

	public function move($size) {
		$this->_check();
		fseek($this->_fp, $size, SEEK_CUR);
	}

	public function moveTo($offset) {
		$this->_check();
		if ($offset >= 0) {
			fseek($this->_fp, $offset, SEEK_SET);
		}
		else {
			fseek($this->_fp, $offset, SEEK_END);
		}
	}

	public function append($strings) {
		if (is_array($strings)) {
			$strings = implode("", $strings);
		}
		fwrite($this->_fp, $strings);
	}

	public function appendLn($strings = "") {
		if (is_array($strings)) {
			$strings = implode("\n", $strings);
		}
		$this->append($strings . "\n");
	}

	public function lock() {
		$this->_check();
		return flock($this->_fp, LOCK_EX);
	}

	public function release() {
		$this->_check();
		return flock($this->_fp, LOCK_UN);
	}
}

?>