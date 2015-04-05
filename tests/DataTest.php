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
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $array = $data->get();
        $this->assertEquals(
        [
            'person'=>
                [
                    '1'=>['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']
                ]
        ],
        $array);
    }

    public function test_add_not_id()
    {
        $dados = [
            'nome'=>
            [
                [['nome'=>'ivo nascimento',"email"=>"iannsp@gmail.com"]]
            ]
        ];
        $expected = [
            'nome'=>[
                ['nome'=>'ivo nascimento',"email"=>"iannsp@gmail.com"]
            ]
        ];
        $data = new Data();
        $data->add($dados);
        $this->AssertEquals($expected, $data->get());
    }
    public function test_init()
    {
        $dados = [
            'nome'=>
            [
                [['nome'=>'ivo nascimento',"email"=>"iannsp@gmail.com"]]
            ]
        ];
        $expected = [
            'nome'=>[
                ['nome'=>'ivo nascimento',"email"=>"iannsp@gmail.com"]
            ]
        ];
        $data = new Data($dados);
        $this->AssertEquals($expected, $data->get());
    }

    public function test_get_item_by_model_without_item_definition()
    {
        $dados = [
            'nome'=>
            [
                [['nome'=>'ivo nascimento',"email"=>"iannsp@gmail.com"]]
            ]
        ];
        $expected = ['nome'=>[['nome'=>'ivo nascimento',"email"=>"iannsp@gmail.com"]]];
        $data = new Data($dados);
        $this->AssertEquals($expected, $data->get(['nome'=>[]]));
    }
    
    public function test_get_by_id()
    {
        $dados = [
            'person'=>[
                [           ['nome'=>'lala',"email"=>"lala@gmail.com"]],
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ];
        $expected = [
            'person'=>[
                0=>['nome'=>'lala',"email"=>"lala@gmail.com"],
                1=>['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ];
        $data = new Data($dados);
        $this->AssertEquals($expected, $data->get(['person'=>[0,1]]));
        
    }
}