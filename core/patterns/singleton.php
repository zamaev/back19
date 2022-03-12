<?php

namespace Core\Patterns;

abstract class Singleton
{
	private static $instances = [];

	private function __construct() {}
	private function __clone() {}

	public function __wakeup()
	{
		throw new Exception('Cannot unserialize a singleton.');
	}

	public static function getInstance(): Singleton
	{
		$class = static::class;
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new static();
		}
		return self::$instances[$class];
	}
}

// если нужна первичная инициализация при создании объекта,
// можно в дочернем классе создать protected construct,
// который будет вызываться Singleton и не будет доступен извне.
/*
	class Test extends Singleton
	{
		public $test;
		protected function __construct()
		{
			$this->test = 123;
		}
	}
*/


