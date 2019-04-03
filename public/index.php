<?php 
 
 namespace pub;

 require_once 'appHelper/PublicHelper.php';

 require_once \pub\appHelper\PublicHelper::getRootDir() . 'vendor/autoload.php';

 \App\controller\Controller::run(); 
