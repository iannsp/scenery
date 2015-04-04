<?php
namespace Iannsp\Scenery;

class PessoaSample
{
    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function save(Data $repositorio)
    {
        $repositorio->add(
        [
            'person'=>[
                ["key"=>$this->data['id'],$this->data]
                    ]
        ]);
    }
}

class SceneryTest extends \PHPUnit_Framework_TestCase
{
    public function assertPreConditions()
    {
        $this->assertTrue(class_exists('Iannsp\Scenery\Scenery'));
    }
    
    public function test_init_data_model()
    {
        $scenery = new Scenery(new Data());
    }
    
    public function test_add_action()
    {
        assert_options(ASSERT_ACTIVE, 1);
        $data = new Data();
        $data->add([
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $scenery = new Scenery($data);
        $scenery->action('Altera Uma Pessoa', function()use ($data){
           $pessoaData = $data->get(['person'=>[1]])['person'][1];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['nome'] = "Ivo Nascimento";
           $pessoa->save($data);
        }, function() use($data){
            $pessoaData = $data->get(['person'=>[1]])['person'][1];
            $pessoa = new PessoaSample($pessoaData);
            assert('$pessoa->data[\'nome\']=="Ivo Nascimento"',"ahhhhhh");
        }
    );
        
    }
    
    public function test_run_actions()
    {
        assert_options(ASSERT_ACTIVE, 1);
        $data = new Data();
        $data->add([
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $scenery = new Scenery($data);
        $scenery->action('Altera Uma Pessoa', function()use ($data){
           $pessoaData = $data->get(['person'=>[1]])['person'][1];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['nome'] = "Ivo Nascimento";
           $pessoa->save($data);
        }, function() use($data){
            $pessoaData = $data->get(['person'=>[1]])['person'][1];
            $pessoa = new PessoaSample($pessoaData);
            assert('$pessoa->data[\'nome\']=="Ivo Nascimento"',"ahhhhhh");
        }
    );
    $result = $scenery->run();
    $this->assertTrue(is_array($result));
    $this->assertArrayHasKey('data', $result);
    }
}

?>