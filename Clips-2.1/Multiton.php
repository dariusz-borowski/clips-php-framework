<?php

class Clips_Multiton {

    private static $_instances = array();

    public static function factory() {

		$key = serialize(func_get_args());

		if (!isset(self::$_instances[$key])) {

			$rc = new ReflectionClass(get_called_class());

			self::$_instances[$key] = $rc -> newInstanceArgs(func_get_args());

		}

		return self::$_instances[$key];

    }

}
