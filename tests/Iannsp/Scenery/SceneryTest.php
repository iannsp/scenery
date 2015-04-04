<?php
namespace Iannsp\Scenery;

class SceneryTest extends \PHPUnit_Framework_TestCase
{
    public function assertPreConditions()
    {
        $this->assertTrue(class_exists('Scenery'));
    }
    
    public function test_init_data_model()
    {
        Scenery::initData([]);
        
    }
}

?>