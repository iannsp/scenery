<?php
namespace Iannsp\Scenery\RunStrategy;

use DateTime;
use Iannsp\Scenery\Scenery;
use Iannsp\Scenery\Connection;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    use Connection;

    /**
     * @test
     */
    public function getInstanceOfStrategyByCycleNumber()
    {
        $strategy = $this->createStrategy(Strategy::RUN_BY_CYCLE_NUMBER);

        $this->assertInstanceOf(ByCycleNumber::class, $strategy);
    }

    /**
     * @test
     *
     * @expectedException \Iannsp\Scenery\RunStrategy\UnsupportedStrategyException
     * @expectedExceptionMessage Strategy Strategy_Invalid is not supported
     */
    public function getInstanceOfStrategyInvalid()
    {
        $this->createStrategy('Strategy_Invalid');
    }

    /**
     * @test
     */
    public function runStrategyByCycleNumber()
    {
        $strategy = $this->createStrategy(Strategy::RUN_BY_CYCLE_NUMBER);
        $this->assertInstanceOf(ByCycleNumber::class, $strategy);

        $result = $strategy->run(
            [
                'cycles' => 1,
                'loud' => false
            ]
        );

        $this->assertCount(1, $result);
    }

    /**
     * @test
     */
    public function runStrategyByUntilDate()
    {
        $strategy = $this->createStrategy(Strategy::RUN_UNTILDATE);
        $this->assertInstanceOf(ByUntilDate::class, $strategy);

        $result = $strategy->run(
            [
                'until' => new DateTime('+2 seconds'),
                'by' => 1,
                'loud' => false
            ]
        );

        $this->assertCount(2, $result);
    }

    /**
     * @param string $name
     *
     * @return Strategy
     */
    private function createStrategy($name)
    {
        $scenery = new Scenery($this->pdo);

        return Factory::get($name, $scenery);
    }
}
