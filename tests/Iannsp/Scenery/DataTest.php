<?php
namespace Iannsp\Scenery;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function assertPreConditions()
    {
        $this->assertTrue(class_exists('Iannsp\Scenery\Data'));
    }
    
    public function test_add()
    {
        $data = new Data();
        $data->add([
            'person'=>['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']
        ]);
    }
}