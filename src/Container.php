<?php

namespace LMSzor\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {

    /**
     * @var array
     */
    private $entries = [];

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ClassNotFoundInGlobalSpaceException
     */
    public function get($id) {
        if(! $this->has($id)) {
            if(! class_exists($id)) {
                throw new ClassNotFoundInGlobalSpaceException($id);
            }

            $this->add($id, $this->createNewEntry($id));
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
     * @throws ClassNotFoundInGlobalSpaceException
     */
    public function createNewEntry(string $id) {
        $reflection = new \ReflectionClass($id);

        if($reflection->getConstructor() === null) {
            return new $id();
        }

        $object = null;
        $argumentsEntries = [];
        $arguments = $reflection->getConstructor()->getParameters();

        foreach($arguments as $argument) {
            $typeHint = $argument->getClass()->getName();

            if($this->has($typeHint)) {
                $argumentsEntries[] = $this->get($typeHint);
                continue;
            }

            $argumentsEntries[] = $this->createNewEntry($typeHint);
        }

        $object = $reflection->newInstanceArgs($argumentsEntries);

        return $object;
    }
}