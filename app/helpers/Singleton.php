<?php


namespace App\helpers;


trait Singleton
{
    private static ?self $self = null;


    public static function init(): self
    {
        if (is_null(static::$self)) static::$self = new self();
        return static::$self;
    }
}