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
        $state  = new \StdClass();
        $result = ['messages'=>[]];
        $state->cycle = $cycleId;
        $state->new = $this->data;
        $state->old = $this->data->newFromDsn;
        $state->loud = $loud;
        foreach($this->actions as $actionItem){
            $state->messages = [];
            $state->new->exec('Begin;');//beginTransaction();
            $actionItem['action']($state);
            $actionItem['expectedDomain']($state);
            if (!is_null($actionItem['expectedInfraStructure']))
                $actionItem['expectedInfraStructure']($state);
            $state->new->exec('Commit');
            $result['messages'] = array_merge($result['messages'], $state->messages);
        }
        return $result;
    }
}
