<?php

namespace LMSzor\Container;

use Psr\Container\NotFoundExceptionInterface;

class EntryNotFound extends \Exception implements NotFoundExceptionInterface {
    public function __construct($id) {
        parent::__construct('Entry "' . $id . '" does not exists in container.');
    }
}