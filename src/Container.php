<?php

namespace LMSzor\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {

    /**
     * @var array
     */
    private $entries = [];

    public function add($id, $entry) {
        if($entry instanceof \Closure) {
            $entry = $entry($this);
        }

        $this->entries[$id] = $entry;
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws EntryNotFound
     */
    public function get($id) {
        if(! $this->has($id)) {
            throw new EntryNotFound($id);
        }

        return $this->entries[$id];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function has($id): bool {
        return isset($this->entries[$id]);
    }

}