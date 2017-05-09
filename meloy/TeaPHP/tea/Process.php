<?php

namespace tea;

/**
 * 多进程抽象类
 */
abstract class Process {
	private $_pid;
	private $_running = false;

	public function __construct() {
		if (!function_exists("pcntl_fork")) {
			throw new \Exception("You must enable pcntl module first");
		}
	}

	/**
	 * 运行子进程
	 */
	public function run() {
		if ($this->_running) {
			throw new \Exception("The process is running");
		}

		$pid = pcntl_fork();
		$this->_running = true;
		if ($pid == -1) {
			throw new \Exception("Fail to fork new process");
		}
		if ($pid > 0) {
			$this->_pid = $pid;
			return;
		}

		if (posix_setsid() == -1) {
			exit("Could not detach from terminal");
		}

		$this->_pid = posix_getpid();
		$this->onStart();
		$this->runProcess();
		$this->onQuit();
		$this->_running = false;

		exit(0);
	}

	/**
	 * 子进程开始时的回调方法
	 */
	public function onStart() {

	}

	/**
	 * 子进程退出时的回调方法
	 */
	public function onQuit() {

	}


	/**
	 * 取得当前子进程的ID
	 *
	 * @return integer
	 */
	public function pid() {
		return $this->_pid;
	}

	/**
	 * 取得父进程的PID
	 *
	 * @return integer
	 */
	public function ppid() {
		return posix_getppid();
	}

	/**
	 * 判断当前子进程是否正在运行
	 *
	 * @return boolean
	 */
	public function isRunning() {
		return $this->_running;
	}

	/**
	 * 运行子进程的实际代码，子类通过覆盖该方法，实现子进程程序
	 */
	abstract public function runProcess();
}

?>