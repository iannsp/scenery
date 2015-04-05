<?php
namespace Iannsp\Scenery\RunStrategy;
use Iannsp\Scenery\Scenery;

class Factory
{
    /**
     * @param unknown $strategyName
     * @param Scenery $scenery
     * @throws \Exception
     * @return Strategy
     */
    public static function get($strategyName, Scenery $scenery){
        if (!self::isValidStrategy($strategyName))
            throw new \Exception("Strategy {$strategyName} does not supported");
        $strategy = "\\Iannsp\\Scenery\\RunStrategy\\{$strategyName}";
        return new $strategy($scenery);
    }

    private static function isValidStrategy($strategyName)
    {
        $strategy = "\\Iannsp\\Scenery\\RunStrategy\\{$strategyName}";
        return  class_exists($strategy);
    }
}