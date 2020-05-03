<?php


namespace App\domain\data;


class ProductIdTransformer
{
      public static function getId(string $productName, int $productNumber): string
      {
          return  $productName . $productNumber;
      }
}