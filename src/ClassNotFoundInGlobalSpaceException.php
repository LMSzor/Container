<?php

namespace LMSzor\Container;

use Psr\Container\NotFoundExceptionInterface;

class ClassNotFoundInGlobalSpaceException extends \Exception implements NotFoundExceptionInterface {

    /**
     * ClassNotFoundInGlobalSpaceException constructor.
     *
     * @param string $id
     */
    public function __construct(string $id) {
        parent::__construct('Class "' . $id . '" has not been found in global space.');
    }
}