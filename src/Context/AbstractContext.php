<?php
namespace Iannsp\Scenery\Context;

abstract class AbstractContext implements Context
{
    private $contextName = 'ABSTRACT';
    private $resources = [];
    private $internalResources = [];
    public function __construct()
    {
        $this->message = Message::getContext($this->contextName);
    }
    public function __get($resourceName)
    {
        if(array_key_exists($resourceName, $this->resources))
            return $this->resources[$resourceName];
        if(method_exists($this, $resourceName)){
            $this->addResource($resourceName, $this->$resourceName());
            return $this->resources[$resourceName];
        }
        return null;
    }
    public function message($message)
    {
        $this->$message->send($message);
    }
    
    protected function addResource($name, $value, $internal = false)
    {
        if ($internal)
            return $this->$internalResources[$name] = $value;
        $this->resources[$name] = $value;
    }
}