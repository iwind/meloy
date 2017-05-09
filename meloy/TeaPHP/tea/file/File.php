<?php

namespace tea\file;

/**
 * 文件对象
 */
class File {
	private $_filename;
	private $_depth = 0;

	/**
	 * 构造器
	 *
	 * @param string $filename 文件名
	 */
	public function __construct($filename) {
		$this->_filename = $filename;
	}

	/**
	 * 判断是否可以执行
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function canExecute() {
		return is_executable($this->_filename);
	}

	/**
	 * 判断该对象是否可读
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function canRead() {
		return is_readable($this->_filename);
	}

	/**
	 * 判断该对象是否可写
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function canWrite() {
		return is_writeable($this->_filename);
	}


	/**
	 * 检查文件/目录是否存在
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function exists() {
		return file_exists($this->_filename);
	}

	/**
	 * 得到文件名
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public function name() {
		return basename($this->_filename);
	}

	/**
	 * 得到上级目录路径
	 *
	 * 如果文件不存在，也返回其所在的目录
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public function parent() {
		return dirname($this->_filename);
	}

	/**
	 * 得到上级文件对象
	 *
	 * @return File
	 * @since 0.0.1
	 */
	public function parentFile() {
		return new File($this->parent());
	}

	/**
	 * 检查当前路径是否为目录
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function isDir() {
		return is_dir($this->_filename);
	}

	/**
	 * 检查当前路径是否为文件
	 *
	 * @return boolean
	 * @since 0.0.1
	 */
	public function isFile() {
		return is_file($this->_filename);
	}

	public function isHidden() {
		$name = $this->name();
		return !(strlen($name) > 0 && $name{0} != ".");
	}

	/**
	 * 取得最后修改时间
	 *
	 * @return integer
	 * @since 0.0.1
	 */
	public function lastModified() {
		if ($this->exists()) {
			return filemtime($this->_filename);
		}
		return null;
	}

	/**
	 * 取得文件尺寸
	 *
	 * @return integer
	 * @since 0.0.1
	 */
	public function length() {
		return filesize($this->_filename);
	}

	/**
	 * 得到绝对路径
	 *
	 * 如果文件不存在，则返回null
	 *
	 * @return string|null
	 * @since 0.0.1
	 */
	public function absPath() {
		if ($this->exists()) {
			return realpath($this->path());
		}
		return null;
	}

	/**
	 * 取得当前对象的路径
	 *
	 * @param boolean $original 是否显示处理前的文件路径
	 * @return string
	 * @since 0.0.1
	 */
	public function path($original = false) {
		return $original ? $this->_filename : preg_replace("/[\\/\\\\]+/", DS, $this->_filename);
	}

	/**
	 * 对文件进行md5计算
	 *
	 * 如果文件不存在，则返回null
	 *
	 * @return string|null
	 * @since 0.0.1
	 */
	public function md5() {
		if ($this->exists() && $this->isFile()) {
			return md5_file($this->_filename);
		}
		return null;
	}

	/**
	 * 读取内容
	 *
	 * @return null|string
	 */
	public function read() {
		if (!$this->isFile()) {
			return null;
		}
		return file_get_contents($this->_filename);
	}

	/**
	 * 获取Reader
	 *
	 * @return FileReader
	 */
	public function reader() {
		return new FileReader($this);
	}

	/**
	 * 获取Writer
	 *
	 * @return FileWriter
	 */
	public function writer() {
		return new FileWriter($this);
	}

	/**
	 * 获取Appender
	 *
	 * @return FileAppender
	 */
	public function appender() {
		return new FileAppender($this);
	}

	/**
	 * 改名为新的文件对象
	 *
	 * @param File $dest 目标文件对象
	 * @since 0.0.1
	 */
	public function renameTo(File $dest) {
		if (rename($this->_filename, $dest->path())) {
			$this->_filename = $dest->path();
		}
	}

	/**
	 * 取得文件模式（八进制）
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public function mode() {
		if ($this->exists()) {
			$perms = fileperms($this->_filename);
			$oct = sprintf("%o", $perms);
			return substr($oct, -4);
		}
		return "0000";
	}

	/**
	 * 返回文件的类型。可能的值有 fifo，char，dir，block，link，file 和 unknown。
	 *
	 * @return string
	 * @since 0.0.1
	 */
	public function type() {
		if ($this->exists()) {
			return filetype($this->_filename);
		}
		return null;
	}

	/**
	 * 取得文件扩展名
	 *
	 * 不含小数点"."符号
	 *
	 * @param boolean $tolower 是否转换为小写
	 * @since 0.0.1
	 * @return string
	 */
	public function ext($tolower = false) {
		$ext = pathinfo($this->_filename, PATHINFO_EXTENSION);
		return $tolower ? strtolower($ext) : $ext;
	}

	/**
	 * 拷贝当前文件或目录到某个路径
	 *
	 * 如果当前对象是文件，但目标是一个目录，则将文件拷贝到目录下。如果两者都是目录，则用当前对象下的文件和目录覆盖目标下的文件和目录。
	 *
	 * @param string|File $path 目标路径
	 * @param callable $filter 过滤器，接收$file对象作为参数
	 * @param boolean $overwrite 是否覆盖
	 * @return boolean
	 * @throws \Exception
	 * @since 0.0.1
	 */
	public function copyTo($path, callable $filter = null, $overwrite = true) {
		if (!$this->exists()) {
			return false;
		}
		if (is_object($path) && ($path instanceof File)) {
			$path = $path->path(true);
		}
		if ($this->isFile()) {
			$dest = $path;
			$dir = null;
			if (is_dir($path)) {
				$dir = f($path);
				$dest = $dir->path() . "/" . $this->name();
			}
			else {
				$dir = f($path)->parentFile();
			}
			if (!$dir->exists() && !@$dir->mkdirs()) {
				throw new Exception("can not copy file from '" . $this->path() . "' to '{$path}' (cannot create folder)");
			}
			if (!$overwrite && file_exists($dest)) {
				return true;
			}
			return copy($this->absPath(), $dest);
		}
		else if ($this->isDir()) {
			$dir = new File($path . "/" . $this->name());
			if (!$overwrite && $dir->exists()) {
				return true;
			}
			if ((!$dir->exists() || !$dir->isDir()) && !@$dir->mkdirs()) {
				throw new Exception("Can not copy file from '" . $this->path() . "' to '{$path}' (cannot create folder)");
			}
			$ret = true;
			$this->each(function (File $file) use ($dir, $filter, $overwrite, &$ret) {
				if ($filter && $filter($file) === false) {
					return;
				}

				$bool = $file->copyTo($dir->path(), $filter, $overwrite);
				if (!$bool) {
					$ret = false;
				}
			});
			return $ret;
		}
		return false;
	}

	/**
	 * 遍历文件
	 *
	 * @param callable $iterator 遍历函数
	 * @param int $maxDepth 最大深度
	 * @param int $depth 当前深度
	 * @since 0.0.1
	 */
	public function each(callable $iterator, $maxDepth = -1, $depth = 0) {
		if (!$this->isDir()) {
			$iterator($this);
			return;
		}
		$files = scandir($this->_filename);
		foreach ($files as $file) {
			if ($file == "." || $file == "..") {
				continue;
			}
			$fileObj = new File($this->absPath() . DS . $file);
			$fileObj->setDepth($depth);
			call_user_func($iterator, $fileObj);

			if (($maxDepth < 0 || $depth < $maxDepth) && $fileObj->isDir()) {
				$fileObj->each($iterator, $maxDepth, $depth + 1);
			}
		}
	}

	/**
	 * 获取当前目录下的文件
	 *
	 * 只读取第一级目录
	 *
	 * @return self[]
	 */
	public function files() {
		$files = [];
		$this->each(function (File $file) use (&$files) {
			$files[] = $file;
		}, 0);
		return $files;
	}

	/**
	 * 获取当前目录下的所有的文件
	 *
	 * 会遍历所有子目录
	 *
	 * @return self[]
	 */
	public function allFiles() {
		$files = [];
		$this->each(function (File $file) use (&$files) {
			$files[] = $file;
		});
		return $files;
	}

	/**
	 * 创建目录
	 *
	 * @param int $mode 权限
	 * @return bool
	 */
	public function mkdirs($mode = 0777) {
		if ($this->isDir()) {
			return true;
		}
		return make_dir($this->_filename, $mode, true);
	}


	/**
	 * 创建一个临时文件
	 *
	 * @param string|null $dir 临时文件所在目录
	 * @param string $prefix 前缀
	 * @return File 成功返回IFile对象，否则返回null
	 * @since 0.0.1
	 */
	 public static function tmp($dir = null, $prefix = "tea_") {
		if (!$dir) {
			$dir = TEA_ROOT . "/tmp/";
		}
		if (!$prefix) {
			$prefix = "";
		}
		$filename = tempnam($dir, $prefix);
		return $filename ? (new File($filename)) : null;
	}

	/**
	 * 深度
	 *
	 * 配合 each() 方法使用
	 *
	 * @return int
	 */
	public function depth() {
		return $this->_depth;
	}

	/**
	 * 设置深度
	 *
	 * 配合 each() 方法使用
	 *
	 * @param int $depth 目录深度
	 */
	public function setDepth($depth) {
		$this->_depth = $depth;
	}
}

?>