<?php
namespace Iannsp\Scenery\RunStrategy;

use Iannsp\Scenery\Scenery;

class ByCycleNumber implements Strategy
{
    private $scenery;
    private $cycleCollection = [];

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
        if (!is_int($rule['cycles'])) { // ['cycles'=>1, 'loud'=>false]
            throw new InsufficientParametersException(
                "rule for run ByCycleNumber Strategy is int totalOfCycles"
            );
        }

        $result = [];

        $cycleId = 0;

        while ($cycleId < $rule['cycles']) {
            $result[$cycleId] = $this->scenery->run($cycleId);

            ++$cycleId;
        }

        return $result;
    }
}
