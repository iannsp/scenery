<?php
namespace Iannsp\Scenery\RunStrategy;

use Iannsp\Scenery\Scenery;

interface Strategy
{
    const RUN_BY_CYCLE_NUMBER = 'ByCycleNumber';
    const RUN_UNTILDATE = 'ByUntilDate';

    public function __construct(Scenery $scenary);

    public function run(array $rule);
}
