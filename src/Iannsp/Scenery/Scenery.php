<?php
namespace Iannsp\Scenery;

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
        callable $expectedRepository= null)
    {
        $this->action[$name] = [
            'action'=>$action,
            'expectedDomain'=>$expectedDomain,
            'expectedRepository'=>$expectedRepository
        ];
    }
    
    public function run()
    {
        foreach($this->action as $actionItem){
            $actionItem['action']();
            $actionItem['expectedDomain']();
            if (!is_null($actionItem['expectedRepository']))
                $actionItem['expectedRepository']();
        }
        return ['data'=>$this->data];
    }
}
