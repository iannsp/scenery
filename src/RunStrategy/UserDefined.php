<?php
namespace Iannsp\Scenery\RunStrategy;

use Iannsp\Scenery\Scenery;

class UserDefined implements Strategy
{
    /**
     * @var Scenery
     */
    private $scenery;

    /**
     * {@inheritdoc}
     */
    public function __construct(Scenery $scenery)
    {
        $this->scenery = $scenery;
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $rule)
    {
    }
}
