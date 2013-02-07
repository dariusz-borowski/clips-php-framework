<?php

interface Clips_View_Interface {

	public function __set($name, $value);
	public function __get($name);
	public function __isset($name);
	public function __unset($name);
	public function load($page, $ext = '.tal');
	public function render($echo = false);

}

