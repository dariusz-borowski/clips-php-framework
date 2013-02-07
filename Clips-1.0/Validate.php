<?php

abstract class Clips_Validate {

	protected function __construct() {
	}

	public function factory($engine, $value, $config = NULL) {

		$c = 'Clips_Validate_'.$engine;

		$adapter = $c::factory($value, $config);

		return $adapter;

	}

}

