<?php
namespace Iannsp\Scenery\RunStrategy;
use Iannsp\Scenery\Scenery;

class ByUntilDate implements Strategy{
    private $scenery;
    private $cycleCollection = [];
    public function __construct(Scenery $scenery)
    {
        $this->scenery = $scenery;
    }
    public function run($rule)
    {
        $result = [];
        if (! $rule['until'] instanceOf \DateTime || !is_numeric($rule['by']))
            throw new \Exception("rule for run BYUntilDate Strategy is [\Datetime until, numeric by]");
        $untilDate = $rule['until'];
        $idOfCycle = 0;
        
        $now = new \DateTime();
        
        $diff = (int)$untilDate->format('U') - (int)$now->format("U");
        while($diff>0){
            $result[$idOfCycle] = $this->scenery->run($idOfCycle, $rule['loud']);
            $idOfCycle++;
            usleep(1000000 * $rule['by']);
            if($rule['loud']){
                ob_start();
                echo "cycle of {$diff}seconds\n";
                ob_end_flush();
            }
            $now = new \DateTime();
            $diff = (int)$untilDate->format('U') - (int)$now->format("U");
        }
        return $result;
    }
    
    
}