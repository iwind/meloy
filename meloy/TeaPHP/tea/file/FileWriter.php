<?php

namespace tea\file;

class FileWriter {
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
			$this->_fp = @fopen($this->_file->path(), "w+");

			if (!$this->_fp) {
				throw new Exception("The file is not writeable");
			}
			chmod($this->_file->absPath(), 0777);
		}
		else {
			$this->_fp = fopen($this->_file->absPath(), "w+");
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

	public function write($string) {
		fwrite($this->_fp, $string);
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