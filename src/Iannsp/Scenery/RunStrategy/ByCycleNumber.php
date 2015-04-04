<?php
namespace Iannsp\Scenery\RunStrategy;
use Iannsp\Scenery\Scenery;

class ByCycleNumber implements Strategy{
    private $scenery;
    private $cycleCollection = [];
    public function __construct(Scenery $scenery)
    {
        $this->scenery = $scenery;
    }
    public function run($rule)
    {
        $result = [];
        if (!is_int($rule))
            throw new \Exception("rule for run ByCycleNumber Strategy is int totalOfCycles");
        $totalOfCyCles = $rule;
        $idOfCycle = 0;
        
        while ($idOfCycle < $rule){
            $result[$idOfCycle] = 
                $this->scenery->run($idOfCycle);
            $idOfCycle++;
        }
        return $result;
    }
    
    
}