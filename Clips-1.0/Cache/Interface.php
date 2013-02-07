<?php

interface Clips_Cache_Interface {

	  public function load($key);
	  public function save($key, $data, $tag, $life);
	  public function delete($key);	  
	  public function deleteTag($tag);
	  public function flush();

}

