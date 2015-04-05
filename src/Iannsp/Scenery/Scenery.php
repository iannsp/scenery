<?php
namespace Iannsp\Scenery;
use Assert\Assertion;
class Scenery{
    private $data;
    private $actions = [];
    public function __construct(\PDO $initData)
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
    
    public function run($cycleId, $loud=false)
    {
        $result = [];
        $state['cycle']= $cycleId;
        $state['new'] = $this->data;
        $state['old'] = $this->data->newFromDsn;
        $state['loud'] = $loud;
        foreach($this->actions as $actionItem){
            $state['new']->exec('Begin;');//beginTransaction();
            $actionItem['action']($state);
            $actionItem['expectedDomain']($state);
            if (!is_null($actionItem['expectedInfraStructure']))
                $actionItem['expectedInfraStructure']($state);
            $state['new']->exec('Commit');
        }
//        return ['data'=>(clone $this->data)]; Tem que virar um dump dos dados
        return ['data'=>(clone $this->data)];
    }
}
