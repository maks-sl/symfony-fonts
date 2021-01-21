<?php

namespace App\ReadModel;

trait FromArrayTrait
{
    public static function fromArray(array $data = [])
    {
        foreach (get_object_vars($obj = new self) as $property => $default) {
            if (!array_key_exists($property, $data)) {
                throw new \InvalidArgumentException('Cannot create object from given array. Missing key');
            };
            $obj->{$property} = $data[$property]; // assign value to field
        }
        return $obj;
    }
}