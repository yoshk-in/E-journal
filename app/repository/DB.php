<?php


namespace App\repository;


class DB
{
    protected static ProductRepository $repository;
    
    public static function setRepository(ProductRepository $productRepository)
    {
        self::$repository = $productRepository;
    }
    
    
    public static function persist($entity)
    {
        (self::$repository)->persist($entity);
    }

    public static function remove($entity)
    {
        (self::$repository)->remove($entity);
    }
}