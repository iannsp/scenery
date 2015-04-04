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
    
    public function run($cycles=1)
    {
        $result = ['cycles'=>[]];
        for($i=0; $i<$cycles; $i++){
            $state['cycle']= $i+1;
            $state['new'] = $this->data;
            foreach($this->action as $actionItem){
                $state['old'] = clone $this->data;
                $actionItem['action']($state);
                $actionItem['expectedDomain']($state);
                if (!is_null($actionItem['expectedRepository']))
                    $actionItem['expectedRepository']($state);
            }
            $result['cycles'][$i]= ['data'=>$this->data];
        }
        return $result;
    }
}
