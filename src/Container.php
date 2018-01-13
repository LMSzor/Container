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
     * @return object
     * @throws ClassNotFoundInGlobalSpaceException
     *
     * @see \Psr\Container\ContainerInterface::get()
     */
    public function get($id) {
        if(!$this->has($id)) {
            $this->add($id);
        }

        return $this->entries[$id];
    }

    /**
     * @param string $id
     *
     * @return bool
     * @see \Psr\Container\ContainerInterface::has()
     */
    public function has($id) {
        return isset($this->entries[$id]);
    }

    /**
     * @param \Closure|EntryProviderInterface|object|string $entry
     *
     * @throws ClassNotFoundInGlobalSpaceException
     */
    public function add($entry) {
        if($entry instanceof \Closure) {
            $entry = $this->createFromClosure($entry);
        } else {
            $object = $this->createObject($entry);

            if($object instanceof EntryProviderInterface) {
                $entry = $object->register();
            } else {
                $entry = $object;
            }
        }

        $this->entries[get_class($entry)] = $entry;
    }

    /**
     * @param string $id
     *
     * @return object
     * @throws ClassNotFoundInGlobalSpaceException
     */
    protected function createObject(string $id) {
        if(!class_exists($id)) {
            throw new ClassNotFoundInGlobalSpaceException($id);
        }

        $reflection = new \ReflectionClass($id);
        $parameters = [];

        if($reflection->getConstructor() !== null) {
            $arguments = $reflection->getConstructor()->getParameters();
            $parameters = $this->prepareReflectionParameters($arguments);
        }

        return $reflection->newInstanceArgs($parameters);
    }

    /**
     * @param \Closure $closure
     *
     * @return mixed
     * @throws ClassNotFoundInGlobalSpaceException
     */
    protected function createFromClosure(\Closure $closure) {
        $reflection = new \ReflectionFunction($closure);

        $arguments = $reflection->getParameters();
        $parameters = $this->prepareReflectionParameters($arguments);

        return $reflection->invokeArgs($parameters);
    }

    /**
     * @param \ReflectionParameter[] $arguments
     *
     * @return array
     * @throws ClassNotFoundInGlobalSpaceException
     */
    protected function prepareReflectionParameters(array $arguments) {
        $parameters = [];

        foreach($arguments as $argument) {
            $typeHint = $argument->getClass()->getName();

            if(!$this->has($typeHint)) {
                $this->add($typeHint);
            }

            $parameters[] = $this->get($typeHint);
        }

        return $parameters;
    }
}