<?php

 namespace App\controller;

 class Controller
 {
	private static $inst;

	private function __construct()
	{
	}

	public static function init()
	{
		if (self::$inst === null) {
			self::$inst = new Controller();
		}
		return self::$inst;
	}

	public function handleRequest()
	{
		$request = \App\base\AppController::getRequest();
		$cmd = \App\base\AppContoller::getCommand($request);
	}

	public static function run()
	{
		$inst = self::init();
		$inst->handleRequest();
	}
 }
