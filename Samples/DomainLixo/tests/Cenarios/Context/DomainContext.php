<?php
namespace Iannsp\Scenery\Context;
use Iannsp\Scenery\Message;

class SampleObjectForDomain
{
    private $property;
    public function set($value)
    {
        $this->property = $value;
    }
}

class DomainContext extends AbstractContext
{
    private $contextName = 'DOMAIN';
    private $message;
    private $resources = [];
    public function __construct()
    {
        $this->message = Message::getContext($this->contextName);

        //sample how to add instance of resource
        $this->addResource('test2', new SampleObjectForDomain());
    }
    
    // sample how to add lazy Resource;
    protected function test()
    {
        return new SampleObjectForDomain();
    }
    
}