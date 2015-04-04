<?php
namespace Iannsp\Scenery;
use Assert\Assertion;
class Scenery{
    private $data;
    private $actions = [];
    public function __construct(Data $initData)
    {
        $this->data = $initData;
    }
    
    public function action($name, 
        callable $action, 
        callable $expectedDomain,
        callable $expectedInfraStructure= null)
    {
        $this->actions[$name] = [
            'action'=>$action,
            'expectedDomain'=>$expectedDomain,
            'expectedInfraStructure'=>$expectedInfraStructure
        ];
    }
    
    public function run($cycleId)
    {
        $result = [];
        $state['cycle']= $cycleId;
        $state['new'] = $this->data;
        foreach($this->actions as $actionItem){
            $state['old'] = clone $this->data;
            $actionItem['action']($state);
            $actionItem['expectedDomain']($state);
            if (!is_null($actionItem['expectedInfraStructure']))
                $actionItem['expectedInfraStructure']($state);
        }
        return ['data'=>(clone $this->data)];
    }
}
