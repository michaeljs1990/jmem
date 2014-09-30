<?php namespace Jmem;

use Exception;

trait ExceptionTrait {

    public function handleError($string) {

        throw new Exception($string);

    }
}