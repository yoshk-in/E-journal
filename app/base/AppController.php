<?php

 namespace App\base;

 class AppContoller
 {
	 private static $request;

	 public static function getRequest()
	 {
		 if (is_null(self::$request))
		 {
			 self::$request = new Request();
		 }
		 return self::$request;
	 }
 }

