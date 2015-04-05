<?php
namespace Iannsp\Scenery\RunStrategy;
use Iannsp\Scenery\Scenery;

class UserDefined implements Strategy{
    private $scenery;
    public function __construct(Scenery $scenery)
    {
        $this->scenery = $scenery;
    }

    public function run(callable $rule)
    {
        
    }
    
    
}