<?php

 namespace base;

 class Request
 {
	 private $data = [];

	 public function __construct()
	 {
		 foreach ($argv as $arg)
		 {
			 list($key, $value) = explode('=', $arg);
			 $this->setData($key, $value);
		 }	 
	 }

	 public function setData($key, $value)
	 {
		 $this->$data[$key] = $value;
	 }

	 public function getData($key)
	 {
		 if (isset($this->data[$key])
		 {
			 return $this->data[$key];
		 }
		 return null;
	 }
 }
