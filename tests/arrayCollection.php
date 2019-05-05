<?php

require_once 'bootstrapTests.php';

$collection = new \Doctrine\Common\Collections\ArrayCollection(array('1'));
var_dump($collection);
var_dump($collection[0]);