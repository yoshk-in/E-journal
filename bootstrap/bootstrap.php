<?php

 use Doctrine\ORM\Tools\Setup;
 use Doctrine\ORM\EntityManager;

 require_once "vendor/autoload.php";

 $config = Setup::createAnnotationMetadataConfiguration(array("app"), true);

 $conn = array(
	 'driver' => 'pdo_mysql',
	 'user' => 'anon',
	 'password' => '',
	 'dbname' => 'mmz'
 );

 $entityManager = EntityManager::create($conn, $config);
