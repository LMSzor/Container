<?php

namespace LMSzor\Container;

interface EntryProviderInterface {
    /**
     * @return object
     */
    public function register();
}