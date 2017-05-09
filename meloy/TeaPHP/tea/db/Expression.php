<?php

namespace tea\db;

/**
 * 定义一个SQL表达式片段
 *
 * 使用 Expression 包装的数据在查询时不会被转义。
 *
 * 可以在 IDbQuery 中的多个方法中使用，如：
 * <code>
 * $query->attr("title", new Expression("title2"))
 * 			->desc(new Expression("RAND()"))
 * 			->findAll();
 * </code>
 * 在这个例子中如果不使用 Expression，那么title2,RAND()都会认为是普通的字符串，要么被认作字段，要么被认作
 * 一个字段的值。使用 Expression 后，我们就知道它们都是表达式的一部分，所以在查询时不会对其进行处理。
 *
 * @since 1.0
 */
class Expression {
	private $_value;

	/**
	 * 构造表达式对象
	 *
	 * @param string $value 表达式内容
	 * @since 1.0
	 */
	public function __construct($value) {
		$this->_value = $value;
	}

	/**
	 * 获取表达式内容
	 *
	 * @return string
	 * @since 1.0
	 */
	public function value() {
		return $this->_value;
	}

	/**
	 * 将当前对象转化为字符串
	 *
	 * @return string
	 * @since 1.0
	 */
	public function __toString() {
		return $this->_value;
	}
}
?>