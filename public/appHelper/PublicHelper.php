<?php

 namespace pub\appHelper;

 Class PublicHelper
 {
	 private static $journalPath = 'mmz/journal/';
	
	 function __construct()
	 {
	 }

	 static function getRootDir()
	 {
		 $dir = __DIR__;
		 $parentDir = strstr($dir, self::$journalPath, true);
		 return $parentDir . self::$journalPath;
	 }
 }
