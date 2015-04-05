<?php
namespace Iannsp\Scenery\RunStrategy;
use Iannsp\Scenery\Scenery;
use Iannsp\Scenery\Data;
use Assert\Assertion;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $pdo;
    public function setUp()
    {
        $dsn ='sqlite:/tmp/sceneryTest.sq3';
        $this->pdo = new \PDO( $dsn);
        $this->pdo->exec('
        drop table person; 
        create table person(id integer primary key, name CHARACTER(100), email CHARACTER(100))');
        $this->pdo->exec("insert into person (name, email) values ('Ivo','iannsp@gmail.com')");
        $this->pdo->newFromDsn = new \PDO( $dsn);
    }
    

    public function test_get_instance_of_Strategy_ByCycleNumber()
    {
        $scenery = new Scenery($this->pdo);
        $runnerStrategy = factory::get(Strategy::RUN_BY_CYCLE_NUMBER,$scenery);
        $this->assertInstanceOf(
            "\\Iannsp\\Scenery\\RunStrategy\\ByCycleNumber", 
            $runnerStrategy);
    }

/**
   * @expectedException Exception
   * @expectedExceptionMessage Strategy Strategy_Invalid does not supported 
*/
    public function test_get_instance_of_Strategy_Invalid()
    {
        $scenery = new Scenery($this->pdo);
        $runnerStrategy = factory::get('Strategy_Invalid',$scenery);
    }
    
    public function test_run_Strategy_ByCycleNumber()
    {
        $scenery = new Scenery($this->pdo);
        $runnerStrategy = factory::get(Strategy::RUN_BY_CYCLE_NUMBER,$scenery);
        $this->assertInstanceOf(
            "\\Iannsp\\Scenery\\RunStrategy\\ByCycleNumber", 
            $runnerStrategy);
            $result = $runnerStrategy->run(['cycles'=>1, 'loud'=>false]);
        $this->assertCount(1, $result);
    }

    public function test_run_Strategy_ByUntilDate()
    {
        $scenery = new Scenery($this->pdo);
        $runnerStrategy = factory::get(Strategy::RUN_UNTILDATE,$scenery);
        $this->assertInstanceOf(
            "\\Iannsp\\Scenery\\RunStrategy\\ByUntilDate", 
            $runnerStrategy);

            $rodarAte = new \Datetime();
            $rodarAte->add(new \DateInterval("P0YT0M2S"));

        $result = $runnerStrategy->run(['until'=>$rodarAte,'by'=>1,'loud'=>false]);
        $this->assertCount(2, $result);
    }
}