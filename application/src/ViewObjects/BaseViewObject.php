<?php


namespace App\ViewObjects;


abstract class BaseViewObject
{
    abstract public static function create($data);
    abstract public function toArray(): array;
}
