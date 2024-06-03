<?php

namespace App\Exceptions;

use Exception;

class ArrayValueError extends Exception {
    private $errorArray;

    public function __construct(array $errorArray) {
        $this->errorArray = $errorArray;
        parent::__construct(json_encode($errorArray));
    }

    public function getErrorArray() {
        return $this->errorArray;
    }
}