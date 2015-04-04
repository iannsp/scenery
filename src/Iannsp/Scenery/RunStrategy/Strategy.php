<?php
namespace Iannsp\Scenery\RunStrategy;


interface Strategy{
    const RUN_BY_CYCLE_NUMBER   = 'ByCycleNumber';
    const RUN_UNTILDATE         = 'BYUntilDate';
    public function __construct(\Iannsp\Scenery\Scenery $scenary);
    public function run($rule);
}