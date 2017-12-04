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
            $this->add($id, $this->createNewEntry($id));
        }

        return $this->entries[$id];
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool {
        return isset($this->entries[$id]);
    }

    public function add($id, $entry = null) {
        if($entry === null) {
            $provider = $this->createNewEntry($id);
            $entry = $provider->register();
            $id = get_class($entry);
        }

        if($entry instanceof \Closure) {
            $entry = $entry($this);
        }

        $this->entries[$id] = $entry;
    }

    /**
     * @param string $id
     *
     * @return object
     * @throws ClassNotFoundInGlobalSpaceException
     */
    private function createNewEntry(string $id) {
        if(! class_exists($id)) {
            throw new ClassNotFoundInGlobalSpaceException($id);
        }

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