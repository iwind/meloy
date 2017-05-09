<?php

namespace tea;

/**
 * 诊断过程中失败时的异常
 *
 */
class TestException extends Exception {
	private $_assertLine = 0;
	private $_assertFile;
	private $_assertFunction;

	public function setAssertLine($line) {
		$this->_assertLine = $line;
	}

	public function setAssertFile($file) {
		$this->_assertFile = $file;
	}

	public function setAssertFunction($function) {
		$this->_assertFunction = $function;
	}

	public function assertLine() {
		return $this->_assertLine;
	}

	public function assertFile() {
		return $this->_assertFile;
	}

	public function assertFunction() {
		return $this->_assertFunction;
	}
}

?>