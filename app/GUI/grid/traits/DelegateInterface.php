<?php


namespace App\GUI\grid\traits;


interface DelegateInterface
{
    function __call($name, $arguments);

    function getMethod($prop, $name, array $arguments = []);

    function setMethod($prop, $name, array $arguments);

    function callComponent($name, $arguments);

    function getComponent();

}