<?php
namespace Iannsp\Scenery\RunStrategy;

use Iannsp\Scenery\Scenery;

class Factory
{
    /**
     * @param string $name
     * @param Scenery $scenery
     *
     * @return Strategy
     *
     * @throws UnsupportedStrategyException
     */
    public static function get($name, Scenery $scenery)
    {
        $strategy = self::getClass($name);

        if (!self::isValidStrategy($strategy)) {
            throw new UnsupportedStrategyException("Strategy {$name} is not supported");
        }

        return new $strategy($scenery);
    }

    /**
     * @param string $strategy
     *
     * @return bool
     */
    private static function isValidStrategy($strategy)
    {
        return class_exists($strategy);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private static function getClass($name)
    {
        return __NAMESPACE__ . '\\' . $name;
    }
}
