<?php


namespace App\CLI\render;


interface IFormatter
{
    public function handle($processed): string ;
}