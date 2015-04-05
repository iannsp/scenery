<?php
namespace Iannsp\Scenery;

class DataTest extends \PHPUnit_Framework_TestCase
{
    public function assertPreConditions()
    {
        $this->assertTrue(class_exists(Data::class));
    }

    /**
     * @test
     */
    public function add()
    {
        $item = ['id' => 1, "name" => 'Ivo', 'email' => 'iannsp@gmail.com'];

        $data = new Data();
        $data->add(['person' => [["key" => '1', $item]]]);

        $this->assertEquals(['person' => ['1' => $item]], $data->get());
    }

    /**
     * @test
     */
    public function addNotId()
    {
        $item = ['nome' => 'ivo nascimento', 'email' => 'iannsp@gmail.com'];

        $data = new Data();
        $data->add(['nome' => [[$item]]]);

        $this->assertEquals(['nome' => [$item]], $data->get());
    }

    /**
     * @test
     */
    public function init()
    {
        $item = ['nome' => 'ivo nascimento', 'email' => 'iannsp@gmail.com'];

        $data = new Data(['nome' => [[$item]]]);
        $this->assertEquals(['nome' => [$item]], $data->get());
    }

    /**
     * @test
     */
    public function getItemByModelWithoutItemDefinition()
    {
        $item = ['nome' => 'ivo nascimento', 'email' => 'iannsp@gmail.com'];

        $data = new Data(['nome' => [[$item]]]);

        $this->assertEquals(['nome' => [$item]], $data->get(['nome' => []]));
    }

    /**
     * @test
     */
    public function getById()
    {
        $item1 = ['nome' => 'lala', 'email' => 'lala@gmail.com'];
        $item2 = ['id' => 1, 'nome' => 'ivo nascimento', 'email' => 'iannsp@gmail.com'];

        $input = [
            'person' => [[$item1], ['key' => '1', $item2]]
        ];

        $expected = [
            'person' => [
                0 => $item1,
                1 => $item2
            ]
        ];

        $data = new Data($input);

        $this->assertEquals($expected, $data->get(['person' => [0, 1]]));
    }
}
