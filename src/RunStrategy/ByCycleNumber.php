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
        //['cycles'=>1, 'loud'=>false]
        if (!is_int($rule['cycles']))
            throw new \Exception("rule for run ByCycleNumber Strategy is int totalOfCycles");
        $totalOfCyCles = $rule['cycles'];
        $idOfCycle = 0;

        while ($idOfCycle < $rule['cycles']){
            $result[$idOfCycle] =
                $this->scenery->run($idOfCycle);
            $idOfCycle++;
        }
        return $result;
    }


}