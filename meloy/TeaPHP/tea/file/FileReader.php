<?php

namespace tea\file;

class FileReader {
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
		if (!$this->_file->canRead()) {
			throw new Exception("The file is not readable");
		}
		$this->_fp = fopen($this->_file->absPath(), "r");
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

	public function read($size) {
		$this->_check();

		if ($size > 0) {
			return fread($this->_fp, $size);
		}

		return null;
	}

	public function readAll() {
		$contents = "";
		while (!$this->isEnd()) {
			$contents .= $this->read(1024);
		}
		return $contents;
	}

	public function readLine() {
		$this->_check();
		return fgets($this->_fp);
	}

	public function readChar() {
		$this->_check();
		return fgetc($this->_fp);
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

	public function isEnd() {
		$this->_check();
		return feof($this->_fp);
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