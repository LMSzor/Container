<?php

namespace LMSzor\Container;

use LMSzor\Container\EntryProviderInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {

    /**
     * @var array
     */
    private $entries = [];

    /**
     * @param string $id
     * @return object
     * @throws ClassNotFoundInGlobalSpaceException
     * @see Psr\Container\ContainerInterface::get()
     */
    public function get($id) {
        if(! $this->has($id)) {
            $this->add($id, $this->createObjectReflection($id));
        }

        return $this->entries[$id];
    }

    /**
     * @param string $id
     * @return bool
     * @see Psr\Container\ContainerInterface::has()
     */
    public function has($id) {
        return isset($this->entries[$id]);
    }
  
  /**
   * @param \Closure|EntryProviderInterface|object $entry
   */
  public function add($entry) {
    if($entry instanceof \Closure) {
      $entry = $this->createClosureReflection($entry);
    } else if(($object = $this->createObjectReflection($entry)) && $object instanceof EntryProviderInterface) {
      $entry = $object->register();
    }
    
    $this->entries[get_class($entry)] = $entry;
  }

  /**
   * @param string $id
   *
   * @return object
   * @throws ClassNotFoundInGlobalSpaceException
   */
  private function createObjectReflection(string $id) {
    if(! class_exists($id)) {
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
   * @return object
   */
  private function createClosureReflection(\Closure $closure) {
    $reflection = new \ReflectionFunction($closure);
      
    $arguments = $reflection->getParameters();
    $parameters = $this->prepareReflectionParameters($arguments);

    return $reflection->invokeArgs($parameters);
  }
  
  /**
   * @param ReflectionParameter[] $arguments
   * @return array[]
   */
  private function prepareReflectionParameters(array $arguments) {
    $parameters = [];
    
    foreach($arguments as $argument) {
      $typeHint = $argument->getClass()->getName();

      if($this->has($typeHint)) {
        $parameters[] = $this->get($typeHint);
        continue;
      }
      
      $parameters[] = $this->createObjectReflection($typeHint);
    }
    
    return $parameters;
  }
}